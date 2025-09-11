(function(){'use strict';
function ready(fn){document.readyState==='loading'?document.addEventListener('DOMContentLoaded',fn):fn();}

function autoPlay(embla, viewport, intervalMs){
  var timer = 0;
  function stop(){ if(timer){ window.clearInterval(timer); timer = 0; } }
  function tick(){
    try {
      if (!embla || typeof embla.scrollNext !== 'function') return;
      if (!embla.canScrollNext()) { embla.scrollTo(0); } else { embla.scrollNext(); }
    } catch(e){}
  }
  function play(){ stop(); timer = window.setInterval(tick, intervalMs); }
  // Prefer Embla events if available; otherwise, fall back to DOM events
  if (embla && typeof embla.on === 'function') {
    embla.on('pointerDown', stop);
    embla.on('select', play);
    embla.on('init', play);
  } else if (viewport) {
    viewport.addEventListener('pointerdown', stop, { passive: true });
  }
  play();
  return { play: play, stop: stop };
}

ready(function(){
  if (typeof EmblaCarousel === 'undefined') return;

  document.querySelectorAll('.gn-embla').forEach(function(root){
    var opts = {};
    try { opts = JSON.parse(root.getAttribute('data-embla')) || {}; } catch(e){}

    var viewport = root.querySelector('.gn-embla__viewport');
    var prevBtn = root.querySelector('.gn-embla__prev');
    var nextBtn = root.querySelector('.gn-embla__next');
    if (!viewport) return;

    var embla = EmblaCarousel(viewport, opts);
    var autoplay = autoPlay(embla, viewport, 3500);

    function setBtnStates(){
      if (!embla) return;
      var canPrev = typeof embla.canScrollPrev === 'function' ? embla.canScrollPrev() : true;
      var canNext = typeof embla.canScrollNext === 'function' ? embla.canScrollNext() : true;
      if (prevBtn) prevBtn.disabled = !canPrev;
      if (nextBtn) nextBtn.disabled = !canNext;
    }

    if (embla && typeof embla.on === 'function') {
      embla.on('select', setBtnStates);
      embla.on('init', setBtnStates);
    } else {
      // Fallback: poll a few times while layout stabilizes
      var tries = 0; var poll = setInterval(function(){ setBtnStates(); if (++tries > 10) clearInterval(poll); }, 300);
    }
    setBtnStates();

    if (prevBtn) prevBtn.addEventListener('click', function(){ if(embla) embla.scrollPrev && embla.scrollPrev(); });
    if (nextBtn) nextBtn.addEventListener('click', function(){ if(embla) embla.scrollNext && embla.scrollNext(); });

    // Keyboard support when slider focused (RTL aware)
    root.setAttribute('tabindex','0');
    root.addEventListener('keydown', function(e){
      if(e.key === 'ArrowRight' && embla && embla.scrollPrev) embla.scrollPrev();
      if(e.key === 'ArrowLeft' && embla && embla.scrollNext) embla.scrollNext();
    });
  });
});
})();


