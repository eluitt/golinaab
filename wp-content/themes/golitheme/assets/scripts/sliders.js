(function(){'use strict';
function ready(fn){document.readyState==='loading'?document.addEventListener('DOMContentLoaded',fn):fn();}

function initGNSwiper(root){
  var viewport = root.querySelector('.gn-embla__viewport');
  var container = viewport && viewport.querySelector('.gn-embla__container');
  var slides = Array.prototype.slice.call(container ? container.querySelectorAll('.gn-embla__slide') : []);
  var edgeCss = parseFloat(getComputedStyle(root).getPropertyValue('--gn-edge-width')) || 96; // 6rem default ~96px
  var prevBtn = root.querySelector('.gn-embla__prev');
  var nextBtn = root.querySelector('.gn-embla__next');
  if(!viewport||!container) return;

  // Options (from data-embla)
  var opts = (function(){ try { return JSON.parse(root.getAttribute('data-embla')||'{}'); } catch(_) { return {}; } })();
  var loop = !!opts.loop;
  var autoplayEnabled = opts.autoplay !== false; // default true
  var respectReducedMotion = opts.respectReducedMotion !== false; // default true; set false to force motion
  var softSnap = opts.softSnap !== false; // default true

  function maxLeft(){ return Math.max(0, viewport.scrollWidth - viewport.clientWidth); }
  function atStart(){ return viewport.scrollLeft <= 1; }
  function atEnd(){ return viewport.scrollLeft >= (maxLeft() - 1); }
  function page(){
    var firstSlide = container.querySelector('.gn-embla__slide');
    var styleGap = parseFloat(getComputedStyle(container).gap || '16');
    var base = firstSlide ? firstSlide.getBoundingClientRect().width : viewport.clientWidth * 0.9;
    return Math.max(1, Math.floor(base + styleGap));
  }
  function snapPositions(){
    return slides.map(function(s){ return s.offsetLeft - edgeCss; }); // compensate container padding (edge)
  }
  function currentIndex(){
    var positions = snapPositions();
    var left = viewport.scrollLeft;
    var idx = 0; var minDiff = Infinity;
    for (var i=0;i<positions.length;i++){ var d = Math.abs(positions[i]-left); if(d<minDiff){minDiff=d; idx=i;} }
    return idx;
  }
  function clampIndex(i){ return Math.max(0, Math.min(i, slides.length-1)); }
  function nearestSnap(left){
    var positions = snapPositions();
    // include hard bounds
    positions.push(0);
    positions.push(maxLeft());
    var closest = positions[0];
    var minDiff = Math.abs(closest - left);
    for (var k=1; k<positions.length; k++){
      var d = Math.abs(positions[k] - left);
      if (d < minDiff){ minDiff = d; closest = positions[k]; }
    }
    return Math.max(0, Math.min(maxLeft(), Math.round(closest)));
  }
  function scrollToIndex(i, duration){
    var positions = snapPositions();
    var idx = clampIndex(i);
    var target = positions[idx];
    if (idx === 0) target = 0;             // first slide should stick to 0
    if (idx === slides.length - 1) {
      // last slide should reach the real end so it is fully visible
      target = maxLeft();
    }
    animateScrollTo(target, duration || 500);
  }

  function updateNav(){
    var disAtStart = atStart();
    var disAtEnd   = atEnd();
    // Visual left button is nextBtn → represents PREV action, disable at start
    if(nextBtn){ nextBtn.disabled = disAtStart; nextBtn.setAttribute('aria-disabled', String(disAtStart)); }
    // Visual right button is prevBtn → represents NEXT action, disable at end
    if(prevBtn){ prevBtn.disabled = disAtEnd; prevBtn.setAttribute('aria-disabled', String(disAtEnd)); }
    root.classList.toggle('is-at-start', disAtStart);
    root.classList.toggle('is-at-end', disAtEnd);
  }

  var timer = 0;
  var resumeTimeout = 0;
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function stop(){ if(timer){ clearInterval(timer); timer=0; } }

  function easeOutQuint(t){ return 1 - Math.pow(1 - t, 5); }
  function animateScrollTo(targetLeft, duration){
    var startLeft = viewport.scrollLeft;
    var distance = targetLeft - startLeft;
    if (Math.abs(distance) < 1) { viewport.scrollLeft = targetLeft; return; }
    var startTime = 0;
    viewport.classList.add('is-animating');
    function step(ts){
      if (!startTime) startTime = ts;
      var elapsed = ts - startTime;
      var progress = Math.min(1, elapsed / duration);
      var eased = easeOutQuint(progress);
      viewport.scrollLeft = startLeft + distance * eased;
      updateNav();
      if (progress < 1) requestAnimationFrame(step);
      else viewport.classList.remove('is-animating');
    }
    requestAnimationFrame(step);
  }
  function scrollToStart(){ animateScrollTo(0, 500); }
  function scrollNext(){
    var positions = snapPositions();
    var idx = currentIndex();
    var nextIdx = Math.min(idx + 1, positions.length - 1);
    var target = (nextIdx === positions.length - 1) ? maxLeft() : positions[nextIdx];
    animateScrollTo(Math.max(0, Math.min(maxLeft(), target)), 450);
  }
  function scrollPrev(){
    var positions = snapPositions();
    var idx = currentIndex();
    var prevIdx = Math.max(idx - 1, 0);
    var target = (prevIdx === 0) ? 0 : positions[prevIdx];
    animateScrollTo(Math.max(0, Math.min(maxLeft(), target)), 450);
  }
  function tick(){ var idx = currentIndex(); if (atEnd()) { loop ? scrollToIndex(0, 500) : stop(); } else { scrollToIndex(idx+1, 500); } }
  function play(){
    stop();
    if (autoplayEnabled === false) return;
    if (respectReducedMotion && reduceMotion) return; // احترام به کاربر
    timer = setInterval(function(){
      if (atEnd()) {
        if (loop) scrollToStart(); else stop();
      } else {
        scrollNext();
      }
    }, 3500);
  }

  viewport.addEventListener('mouseenter', stop);
  viewport.addEventListener('mouseleave', play);
  viewport.addEventListener('focusin', stop);
  viewport.addEventListener('focusout', play);

  var dragging=false, startX=0, startLeft=0, velocity=0, lastX=0, rafId=0, rafDrag=0;
  var overscrollAmount=0, overscrollDir=0; // -1 left, +1 right
  function onDown(e){
    dragging=true; velocity=0; lastX=0;
    startX=(e.touches?e.touches[0].clientX:e.clientX);
    startLeft=viewport.scrollLeft;
    viewport.classList.add('is-grabbing');
    viewport.classList.add('is-dragging');
    stop(); if (resumeTimeout) { clearTimeout(resumeTimeout); resumeTimeout = 0; }
    // reset overscroll visuals
    overscrollAmount = 0; overscrollDir = 0;
    container.style.transition = 'none';
    container.style.transform = 'translateX(0px)';
  }
  function onMove(e){
    if(!dragging) return;
    var x = e.clientX != null ? e.clientX : (e.touches?e.touches[0].clientX:0);
    if(lastX!==0){ velocity = x - lastX; }
    lastX = x;
    var tentative = startLeft - (x - startX);
    var maxLeft = viewport.scrollWidth - viewport.clientWidth;
    overscrollAmount = 0; overscrollDir = 0;
    if (tentative < 0) {
      // left edge rubber-band → move container visually to the right
      overscrollAmount = Math.min(120, Math.abs(tentative)) * 0.35;
      overscrollDir = +1;
      tentative = 0;
    } else if (tentative > maxLeft) {
      // right edge rubber-band → move container visually to the left
      overscrollAmount = Math.min(120, tentative - maxLeft) * 0.35;
      overscrollDir = -1;
      tentative = maxLeft;
    }
    if (rafDrag) cancelAnimationFrame(rafDrag);
    rafDrag = requestAnimationFrame(function(){
      viewport.scrollLeft = tentative;
      if (overscrollAmount) {
        container.style.transform = 'translateX(' + (overscrollAmount * overscrollDir) + 'px)';
      } else {
        container.style.transform = 'translateX(0px)';
      }
    });
  }
  function animateInertia(){
    var maxLeft = viewport.scrollWidth - viewport.clientWidth;
    viewport.scrollLeft += velocity;
    if (viewport.scrollLeft < 0) { viewport.scrollLeft = 0; velocity = 0; }
    else if (viewport.scrollLeft > maxLeft) { viewport.scrollLeft = maxLeft; velocity = 0; }
    updateNav();
    velocity *= 0.95; // friction only, no spring
    if (Math.abs(velocity) < 0.08) {
      cancelAnimationFrame(rafId);
      viewport.classList.remove('is-animating');
      updateNav();
      return;
    }
    rafId = requestAnimationFrame(animateInertia);
  }
  function onUp(){
    if(!dragging) return;
    dragging=false;
    viewport.classList.remove('is-grabbing');
    viewport.classList.remove('is-dragging');
    if (rafId) cancelAnimationFrame(rafId);
    if (rafDrag) cancelAnimationFrame(rafDrag);
    velocity = 0; // we manage final position ourselves
    viewport.classList.remove('is-animating');
    if (overscrollAmount) {
      // bounce back container transform to equilibrium
      container.style.transition = 'transform 260ms cubic-bezier(.2,.8,.2,1)';
      container.style.transform = 'translateX(0px)';
      // clear after transition
      setTimeout(function(){ container.style.transition = 'none'; }, 280);
    }
    // soft snap to the nearest slide start
    if (softSnap) {
      var target = nearestSnap(viewport.scrollLeft);
      animateScrollTo(target, 320);
    }
    updateNav();
    if (resumeTimeout) clearTimeout(resumeTimeout);
    resumeTimeout = setTimeout(play, 1200);
  }
  // Mouse/touch listeners (document-level move to allow dragging outside viewport)
  // Use Pointer Events for unified drag handling
  var activePointerId = null;
  function onPointerDown(e){
    if (e.pointerType === 'mouse' && e.button !== 0) return; // left button only
    activePointerId = e.pointerId;
    viewport.setPointerCapture(activePointerId);
    onDown(e);
  }
  function onPointerMove(e){ if (activePointerId !== e.pointerId) return; onMove(e); }
  function onPointerUp(e){ if (activePointerId !== e.pointerId) return; viewport.releasePointerCapture(activePointerId); activePointerId = null; onUp(e); }
  viewport.addEventListener('pointerdown', onPointerDown);
  document.addEventListener('pointermove', onPointerMove, {passive:false});
  document.addEventListener('pointerup', onPointerUp);
  document.addEventListener('pointercancel', onPointerUp);

  // Prevent click-through on links after drag
  var dragged = false;
  root.addEventListener('mousedown', function(){ dragged = false; });
  document.addEventListener('mousemove', function(e){ if(dragging) dragged = true; }, {passive:true});
  root.addEventListener('click', function(e){ if(dragged){ e.preventDefault(); e.stopPropagation(); dragged=false; } }, true);

  // Invert buttons to match visual RTL expectation
  // Arrows: left button shows previous items, right button shows next items
  // Visual right button (prevBtn) should go NEXT
  if(prevBtn) prevBtn.addEventListener('click', function(e){ e.preventDefault(); scrollNext(); });
  // Visual left button (nextBtn) should go PREV
  if(nextBtn) nextBtn.addEventListener('click', function(e){ e.preventDefault(); scrollPrev(); });

  // Remove focus/keyboard behavior per UX request (no focus ring, no keyboard)

  viewport.addEventListener('scroll', function(){ updateNav(); }, {passive:true});
  window.addEventListener('resize', function(){ updateNav(); }, {passive:true});
  window.addEventListener('pageshow', function(e){ if(e.persisted) play(); });
  document.addEventListener('visibilitychange', function(){ if(!document.hidden) play(); });
  if (window.matchMedia) {
    try {
      var mq = window.matchMedia('(prefers-reduced-motion: reduce)');
      mq.addEventListener ? mq.addEventListener('change', function(e){ reduceMotion = e.matches; if(!(respectReducedMotion && reduceMotion)) play(); else stop(); })
                         : mq.addListener(function(e){ reduceMotion = e.matches; if(!(respectReducedMotion && reduceMotion)) play(); else stop(); });
    } catch(_) {}
  }

  updateNav();
  root.classList.add('is-initialized');
  play();
}

ready(function(){
  document.querySelectorAll('.gn-embla').forEach(function(root){
    // read options if needed later (e.g., autoplay off)
    try {
      var opts = JSON.parse(root.getAttribute('data-embla')||'{}');
      root.__gn_opts = opts;
    } catch(_){}
    initGNSwiper(root);
  });
});
})();


