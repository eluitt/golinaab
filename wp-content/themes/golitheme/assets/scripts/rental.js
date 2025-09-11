(function(){
  'use strict';
  function ready(fn){ document.readyState==='loading'?document.addEventListener('DOMContentLoaded',fn):fn(); }
  ready(function(){
    var form = document.getElementById('gn-rental-form');
    if(!form) return;
    var fb = document.getElementById('gn-rental-feedback');
    function setMsg(msg, ok){ if(fb){ fb.textContent = msg; fb.style.color = ok? '#16a34a' : '#991b1b'; } }

    form.addEventListener('submit', function(e){
      e.preventDefault();
      var data = new FormData(form);
      var s = data.get('start_date');
      var eDate = data.get('end_date');
      if(!s || !eDate){ return setMsg('تاریخ‌ها را تکمیل کنید.', false); }
      if(new Date(eDate) < new Date(s)){ return setMsg('تاریخ پایان نمی‌تواند قبل از شروع باشد.', false); }
      var ack = data.get('deposit_ack');
      if(!ack){ return setMsg('پذیرش شرایط الزامی است.', false); }
      setMsg('در حال ارسال...', true);
      fetch((window.gn_ajax && gn_ajax.ajax_url) || '/wp-admin/admin-ajax.php', { method:'POST', body:data })
        .then(function(r){ return r.json(); })
        .then(function(res){
          if(res && res.success){ setMsg('درخواست با موفقیت ثبت شد.', true); form.reset(); }
          else { setMsg(res && res.message ? res.message : 'خطا در ارسال فرم.', false); }
        })
        .catch(function(){ setMsg('ارتباط با سرور برقرار نشد.', false); });
    });
  });
})();
