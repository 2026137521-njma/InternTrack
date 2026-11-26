// Shared page helpers and simulated session checks.
const page = document.body.dataset.page;
const $ = id => document.getElementById(id);
const user = users().find(u => u.id === sessionStorage.getItem('it4_user'));
const home = u => u.role === 'employer' ? 'employer.html' : 'dashboard.html';
const esc = value => String(value).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
function message(text, type='danger') { $('message').className='alert alert-'+type; $('message').textContent=text; }
function company(id) { return companies.find(c=>c[0]===Number(id)); }
function dateKey(d) { return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0'); }
// Login and registration follow the lecturer's form / message / redirect flow.
if (page==='index' || page==='register') {
 if(user) location.replace(home(user));
 if(new URLSearchParams(location.search).has('registered')) message('Your registration was successful. Please log in.','success');
 $('authForm').onsubmit = event => {
  event.preventDefault();
  const email=$('email').value.trim().toLowerCase(), password=$('password').value;
  const list=users();
  if(page==='register') {
   if(!$('name').value.trim() || password.length<6) return message('Enter a name and at least 6 password characters.');
   if(list.some(u=>u.email===email)) return message('Email already registered.');
   list.push({id:crypto.randomUUID(),name:$('name').value.trim(),email,password,role:'student'});
   save('users',list); location.href='index.html?registered=1';
  } else {
   const found=list.find(u=>u.email===email && u.password===password);
   if(!found) return message('Invalid email or password.');
   sessionStorage.setItem('it4_user',found.id); location.href=home(found);
  }
 };
} else if(!user) { location.replace('index.html'); }
else if((page==='employer') !== (user.role==='employer')) { location.replace(home(user)); }
else {
 // Role-based navigation.
 $('nav').innerHTML=(user.role==='student' ? '<a href="dashboard.html">Dashboard</a><a href="companies.html">Companies</a><a href="applications.html">Applications</a><a href="resume.html">Resume</a>' : '<a href="employer.html">Dashboard</a>')+'<button class="logout" id="logout">Logout</button>';
 $('logout').onclick=()=>{sessionStorage.removeItem('it4_user');location.href='index.html';};
 const all=read('applications'); const mine=all.filter(a=>a.user_id===user.id);
 if(page==='dashboard') {
  $('welcome').textContent='Welcome, '+user.name+'. Find your placement and monitor your progress.';
  const labels=statuses, values=labels.map(s=>mine.filter(a=>a.status===s).length);
  $('stats').innerHTML=['Total applications',...labels].map((s,i)=>`<div class="col"><article class="panel stat h-100"><p>${s}</p><strong>${i===0?mine.length:values[i-1]}</strong></article></div>`).join('');
  const weeks=[0,0,0,0], weekLabels=[];
  for(let i=0;i<28;i++){const d=new Date();d.setDate(d.getDate()-27+i);weeks[Math.floor(i/7)]+=mine.filter(a=>a.date===dateKey(d)).length;if(i%7===0)weekLabels.push(d.toLocaleDateString('en-GB',{day:'numeric',month:'short'}));}
  // Display the charts using the prepared labels and values.
  displayCharts(labels, values, weekLabels, weeks, mine.length);

 }
 if(page==='companies') $('companies').innerHTML=companies.map(c=>`<article class="panel company-card"><div class="initial">${c[1].slice(0,2)}</div><h2>${c[1]}</h2><p class="muted">${c[2]} · ${c[3]}</p><ul>${c[4].map(s=>`<li>${s}</li>`).join('')}</ul><a href="applications.html?company=${c[0]}">Apply now</a></article>`).join('');
 if(page==='resume') {
  const show=()=>{const r=read('resume_'+user.id,null);$('currentResume').textContent=r?'Current resume: '+r.name:'No resume uploaded.';};show();
  $('resumeForm').onsubmit=e=>{e.preventDefault();const f=$('pdf').files[0];if(!f || !f.name.toLowerCase().endsWith('.pdf') || f.size>1048576)return message('Choose a PDF under 1 MB.');const reader=new FileReader();reader.onload=()=>{try{save('resume_'+user.id,{name:f.name,data:reader.result});message('Resume uploaded.','success');show();}catch{message('Browser storage is full.');}};reader.readAsDataURL(f);};
 }
 if(page==='applications') {
  $('company').innerHTML=companies.map(c=>`<option value="${c[0]}">${c[1]}</option>`).join('');
  const selected=new URLSearchParams(location.search).get('company');if(company(selected))$('company').value=selected;
  const positions=()=>{$('position').innerHTML=company($('company').value)[4].map(s=>`<option>${s}</option>`).join('');};positions();$('company').onchange=positions;
  $('applyForm').onsubmit=e=>{e.preventDefault();const resume=read('resume_'+user.id,null), cid=Number($('company').value),position=$('position').value; if(!resume)return message('Upload your resume first.');const list=read('applications');if(list.some(a=>a.user_id===user.id&&a.company_id===cid&&a.position===position))return message('You already applied for this position.');list.push({id:crypto.randomUUID(),user_id:user.id,company_id:cid,position,date:dateKey(new Date()),status:'Pending',resume});try{save('applications',list);location.reload();}catch{message('Browser storage is full.');}};
 }
 // Application records and offer decisions.
 if(page==='applications'||page==='employer') {
  const employer=page==='employer', records=employer?all.filter(a=>a.company_id===user.company_id):mine;
  if(employer){$('welcome').textContent=company(user.company_id)[1]+' Dashboard';$('summary').textContent=records.length+' applications received';}
  $('records').innerHTML=records.length?'<table class="table table-dark"><thead><tr><th>'+ (employer?'Student':'Company')+'</th><th>Position</th><th>Date</th><th>Status</th><th>Action</th></tr></thead><tbody>'+records.map(a=>`<tr><td>${esc(employer?(users().find(u=>u.id===a.user_id)?.name||'Student'):company(a.company_id)[1])}</td><td>${esc(a.position)}</td><td>${esc(a.date)}</td><td>${esc(a.status)}</td><td>${employer?`<button class="btn btn-sm btn-outline-light" data-resume="${a.id}">Resume</button> `:''}${(employer&&a.status==='Pending'?['Offered','Rejected']:!employer&&a.status==='Offered'?['Offer Accepted','Offer Declined']:[]).map(s=>`<button class="btn btn-sm btn-outline-light" data-id="${a.id}" data-status="${s}">${s}</button>`).join(' ')}</td></tr>`).join('')+'</tbody></table>':'<p>No applications yet.</p>';
  $('records').onclick=e=>{const b=e.target.closest('button');if(!b)return;if(b.dataset.resume){const a=records.find(a=>a.id===b.dataset.resume);const link=document.createElement('a');link.href=a.resume.data;link.download=a.resume.name;link.click();return;}const list=read('applications'),a=list.find(a=>a.id===b.dataset.id);if(!a||!records.some(r=>r.id===a.id))return;const allowed=employer&&a.status==='Pending'?['Offered','Rejected']:!employer&&a.status==='Offered'?['Offer Accepted','Offer Declined']:[];if(allowed.includes(b.dataset.status)){a.status=b.dataset.status;save('applications',list);location.reload();}};
 }
}
