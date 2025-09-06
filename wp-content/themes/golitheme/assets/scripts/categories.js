(function(){'use strict';
function ready(fn){document.readyState==='loading'?document.addEventListener('DOMContentLoaded',fn):fn();}

function openModal(modal){
  if(!modal) return;
  modal.setAttribute('aria-hidden','false');
  document.body.classList.add('gn-modal-open');
  const focusable = modal.querySelectorAll('button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])');
  const first = focusable[0], last = focusable[focusable.length-1];
  function trap(e){
    if(e.key==='Tab'){
      if(e.shiftKey && document.activeElement===first){ last.focus(); e.preventDefault(); }
      else if(!e.shiftKey && document.activeElement===last){ first.focus(); e.preventDefault(); }
    } else if(e.key==='Escape'){ closeModal(modal); }
  }
  modal.__trap = trap;
  document.addEventListener('keydown', trap);
  first && first.focus();
}
function closeModal(modal){
  if(!modal) return;
  modal.setAttribute('aria-hidden','true');
  document.body.classList.remove('gn-modal-open');
  document.removeEventListener('keydown', modal.__trap || (()=>{}));
}
ready(function(){
  document.querySelectorAll('[data-gn-open]').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const id = btn.getAttribute('data-gn-open');
      const modal = document.getElementById(`gn-${id}`) || document.getElementById(id);
      openModal(modal);
    });
  });
  document.addEventListener('click',function(e){
    const target = e.target;
    if(target.matches('[data-gn-close]')){ closeModal(target.closest('.gn-modal')); }
    if(target.classList.contains('gn-modal__backdrop')){ closeModal(target.closest('.gn-modal')); }
  });
});
})();


