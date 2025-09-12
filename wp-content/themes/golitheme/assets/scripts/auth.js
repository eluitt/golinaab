// Lightweight animations for account page (login/register)
(function(){
  function onReady(fn){ if(document.readyState!=='loading') fn(); else document.addEventListener('DOMContentLoaded',fn); }
  onReady(function(){
    var root = document.querySelector('.gn-login-card');
    if(!root) return;

    // Centering class already on container; add fade-in
    root.style.opacity = '0';
    root.style.transform = 'translateY(16px)';
    requestAnimationFrame(function(){
      root.style.transition = 'opacity .4s ease, transform .4s ease';
      root.style.opacity = '1';
      root.style.transform = 'translateY(0)';
    });

    // Tab switching with underline indicator
    var tabs = Array.prototype.slice.call(document.querySelectorAll('.gn-tab-btn'));
    var panels = Array.prototype.slice.call(document.querySelectorAll('.gn-tab-panel'));
    if(tabs.length){
      // sliding underline indicator
      var nav = document.querySelector('.gn-tab-nav');
      var indicator = document.createElement('span');
      indicator.className='gn-tab-indicator';
      if(nav){ nav.appendChild(indicator); }

      function moveIndicator(btn){
        var parentEl = btn.parentElement; // .gn-tab-nav
        var left = btn.offsetLeft;
        var width = btn.offsetWidth;
        // account for padding/margins by using offset
        indicator.style.transform = 'translateX(' + left + 'px)';
        indicator.style.width = width + 'px';
      }
      setTimeout(function(){ moveIndicator(document.querySelector('.gn-tab-btn.active')||tabs[0]); },0);

      tabs.forEach(function(btn){
        btn.addEventListener('click',function(){
          tabs.forEach(function(b){ b.classList.remove('active'); b.style.color='#82638a'; b.style.outline='none'; });
          btn.classList.add('active'); btn.style.color='#4b2a5a';
          panels.forEach(function(p){ p.style.display='none'; p.style.opacity='0'; p.style.transform='translateX(20px)'; });
          var target = document.querySelector(btn.dataset.target);
          if(target){
            target.style.display='block';
            requestAnimationFrame(function(){
              target.style.transition='opacity .35s ease, transform .35s ease';
              target.style.opacity='1';
              target.style.transform='translateX(0)';
            });
          }
          moveIndicator(btn);
        });
      });
    }

    // Inputs: golden focus ring
    Array.prototype.slice.call(document.querySelectorAll('.gn-account input')).forEach(function(el){
      el.style.border='1px solid #C8A2C8';
      el.style.borderRadius='1rem';
      el.style.padding='.75rem';
      el.style.background='#f6f0fa';
      el.style.color='#4b2a5a';
      el.addEventListener('mouseenter', function(){ el.style.borderColor='#B08FB0'; });
      el.addEventListener('mouseleave', function(){ el.style.borderColor='#C8A2C8'; });
      el.addEventListener('focus', function(){ el.style.borderColor='#D4AF37'; el.style.boxShadow='0 0 0 3px rgba(212,175,55,.25)'; el.style.outline='none'; });
      el.addEventListener('blur', function(){ el.style.boxShadow='none'; });
    });

    // Buttons: purple background, soft hover
    Array.prototype.slice.call(document.querySelectorAll('.gn-account-btn')).forEach(function(btn){
      btn.style.background='#C8A2C8';
      btn.style.color='#2C2C2C';
      btn.style.border='1px solid #D4AF37';
      btn.style.borderRadius='1rem';
      btn.style.padding='.75rem';
      btn.style.fontWeight='600';
      btn.style.cursor='pointer';
      btn.style.transition='background-color .15s ease, box-shadow .15s ease';
      btn.addEventListener('mouseenter', function(){ btn.style.background='#B08FB0'; btn.style.boxShadow='none'; });
      btn.addEventListener('mouseleave', function(){ btn.style.background='#C8A2C8'; btn.style.boxShadow='none'; });
      btn.addEventListener('focus', function(){ btn.style.boxShadow='0 0 0 3px rgba(212,175,55,.25)'; btn.style.outline='none'; });
      btn.addEventListener('blur', function(){ btn.style.boxShadow='none'; });
    });

  });
})();


