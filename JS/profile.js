/* profile.js
   Handles loading and saving personal information to localStorage.
*/
(function(){
  const STORAGE_KEY = 'personalInfo';

  function loadData(){
    try{
      const raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return null;
      return JSON.parse(raw);
    }catch(e){ return null; }
  }

  function saveData(data){
    try{
      localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
      return true;
    }catch(e){ return false; }
  }

  function populateForm(form, data){
    if (!form || !data) return;
    Object.keys(data).forEach(k => {
      const el = form.querySelector('#'+k);
      if (el) el.value = data[k];
    });
  }

  document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('personalForm');
    if (!form) return;

    // load existing data
    const data = loadData();
    if (data) populateForm(form, data);

    // cancel button should revert to last saved values
    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn){
      cancelBtn.addEventListener('click', function(e){
        const saved = loadData();
        if (saved) populateForm(form, saved);
        else form.reset();
      });
    }

    form.addEventListener('submit', function(e){
      e.preventDefault();
      // if any invalid fields exist (validation.js may have marked them), do not save
      if (form.querySelector('.invalid')){
        const first = form.querySelector('.invalid');
        if (first) first.focus();
        return;
      }

      const formData = {
        firstName: (document.getElementById('firstName')||{value:''}).value,
        lastName: (document.getElementById('lastName')||{value:''}).value,
        email: (document.getElementById('email')||{value:''}).value,
        phone: (document.getElementById('phone')||{value:''}).value,
        address: (document.getElementById('address')||{value:''}).value,
        city: (document.getElementById('city')||{value:''}).value,
        country: (document.getElementById('country')||{value:''}).value
      };

      const ok = saveData(formData);
      if (ok) {
        // show a small saved message instead of alert for nicer UX
        const prev = document.getElementById('profileSavedMsg');
        if (prev) prev.remove();
        const msg = document.createElement('div');
        msg.id = 'profileSavedMsg';
        msg.textContent = 'Your changes have been saved.';
        msg.style.marginTop = '10px';
        msg.style.color = 'var(--success-text)';
        form.parentNode.insertBefore(msg, form.nextSibling);
        setTimeout(()=>{ try{ msg.remove(); }catch(e){} }, 2500);
      } else {
        alert('Unable to save changes to localStorage.');
      }
    });

  });

})();
