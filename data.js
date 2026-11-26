// Sample company directory and browser storage helpers.
const companies = [
 [1,'Nexa Digital','Technology','Kuala Lumpur',['Web Developer Intern','Software Intern','HR Intern']],
 [2,'Pixel Studio','Creative Design','Petaling Jaya',['UI/UX Intern','Design Intern','Marketing Intern']],
 [3,'CloudWorks','IT Services','Shah Alam',['IT Support Intern','Network Intern','Finance Intern']],
 [4,'DataBridge','Data Analytics','Cyberjaya',['Data Analyst Intern','Records Management Intern','Admin Intern']],
 [5,'Bright Media','Digital Marketing','Kuala Lumpur',['Content Creator Intern','Social Media Intern','Business Intern']],
 [6,'TechNova','Technology Solutions','Cyberjaya',['System Support Intern','Cybersecurity Intern','Project Management Intern']]
];
const statuses = ['Pending','Offered','Rejected','Offer Accepted','Offer Declined'];
function read(key, fallback = []) { return JSON.parse(localStorage.getItem('it4_'+key) || JSON.stringify(fallback)); }
function save(key, value) { localStorage.setItem('it4_'+key, JSON.stringify(value)); }
function users() { return read('users', companies.map(c => ({id:'hr'+c[0],name:'Employer '+c[0],email:'hr'+c[0]+'@interntrack.test',password:'employer123',role:'employer',company_id:c[0]}))); }
