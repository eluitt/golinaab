(function(){'use strict';
function ready(fn){document.readyState==='loading'?document.addEventListener('DOMContentLoaded',fn):fn();}

function autoPlay(embla, intervalMs){
  let timer = 0;
  function play(){ stop(); timer = window.setInterval(()=>{ if(!embla.canScrollNext()){ embla.scrollTo(0); } else { embla.scrollNext(); } }, intervalMs); }
  function stop(){ if(timer){ window.clearInterval(timer); timer = 0; } }
  embla.on('pointerDown', stop);
  embla.on('select', play);
  embla.on('init', play);
  play();
  return { play, stop };
}

ready(function(){
  if (typeof EmblaCarousel === 'undefined') return;

  document.querySelectorAll('.gn-embla').forEach(function(root){
    var opts = {};
    try { opts = JSON.parse(root.getAttribute('data-embla')) || {}; } catch(e){}

    var viewport = root.querySelector('.gn-embla__viewport');
    var prevBtn = root.querySelector('.gn-embla__prev');
    var nextBtn = root.querySelector('.gn-embla__next');

    var embla = EmblaCarousel(viewport, opts);
    var autoplay = autoPlay(embla, 3500);

    function setBtnStates(){
      var canPrev = embla.canScrollPrev();
      var canNext = embla.canScrollNext();
      if (prevBtn) prevBtn.disabled = !canPrev;
      if (nextBtn) nextBtn.disabled = !canNext;
    }

    embla.on('select', setBtnStates);
    embla.on('init', setBtnStates);
    setBtnStates();

    if (prevBtn) prevBtn.addEventListener('click', function(){ embla.scrollPrev(); });
    if (nextBtn) nextBtn.addEventListener('click', function(){ embla.scrollNext(); });

    // Keyboard support when slider focused (RTL aware)
    root.setAttribute('tabindex','0');
    root.addEventListener('keydown', function(e){
      if(e.key === 'ArrowRight') embla.scrollPrev();
      if(e.key === 'ArrowLeft') embla.scrollNext();
    });
  });
});
})();


