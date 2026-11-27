document.getElementById('menuToggle')?.addEventListener('click',()=>document.getElementById('sidebar')?.classList.toggle('open'));
document.querySelectorAll('.password-toggle').forEach(button=>button.addEventListener('click',()=>{const input=document.getElementById(button.dataset.target);input.type=input.type==='password'?'text':'password';}));
