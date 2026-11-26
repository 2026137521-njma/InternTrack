CREATE DATABASE IF NOT EXISTS interntrack_v3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE interntrack_v3;
CREATE TABLE IF NOT EXISTS companies (id INT AUTO_INCREMENT PRIMARY KEY, company_name VARCHAR(150) NOT NULL, industry VARCHAR(100) NOT NULL, location VARCHAR(150) NOT NULL);
CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, role ENUM('student','employer') NOT NULL DEFAULT 'student', company_id INT NULL, FOREIGN KEY(company_id) REFERENCES companies(id));
CREATE TABLE IF NOT EXISTS applications (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, company_id INT NOT NULL, position VARCHAR(150) NOT NULL, application_date DATE NOT NULL, status ENUM('Pending','Offered','Rejected','Offer Accepted','Offer Declined') NOT NULL DEFAULT 'Pending', resume_pdf MEDIUMBLOB NOT NULL, UNIQUE(user_id,company_id,position), FOREIGN KEY(user_id) REFERENCES users(id), FOREIGN KEY(company_id) REFERENCES companies(id));
INSERT IGNORE INTO companies VALUES
 (1,'Nexa Digital','Technology','Kuala Lumpur'),
 (2,'Pixel Studio','Creative Design','Petaling Jaya'),
 (3,'CloudWorks','IT Services','Shah Alam'),
 (4,'DataBridge','Data Analytics','Cyberjaya');
CREATE TABLE IF NOT EXISTS positions (id INT AUTO_INCREMENT PRIMARY KEY, company_id INT NOT NULL, position VARCHAR(150) NOT NULL, department VARCHAR(100) NOT NULL, UNIQUE(company_id,position), FOREIGN KEY(company_id) REFERENCES companies(id));
CREATE TABLE IF NOT EXISTS resumes (user_id INT PRIMARY KEY, filename VARCHAR(255) NOT NULL, pdf MEDIUMBLOB NOT NULL, FOREIGN KEY(user_id) REFERENCES users(id));
INSERT IGNORE INTO companies VALUES (5,'Bright Media','Digital Marketing','Kuala Lumpur'),(6,'TechNova','Technology Solutions','Cyberjaya');
INSERT IGNORE INTO positions (company_id,position,department) VALUES
(1,'Web Developer Intern','IT'),(1,'Software Intern','Engineering'),(1,'HR Intern','Human Resources'),
(2,'UI/UX Intern','Product'),(2,'Design Intern','Creative'),(2,'Marketing Intern','Marketing'),
(3,'IT Support Intern','IT'),(3,'Network Intern','Infrastructure'),(3,'Finance Intern','Finance'),
(4,'Data Analyst Intern','Analytics'),(4,'Records Management Intern','Records'),(4,'Admin Intern','Administration'),
(5,'Content Creator Intern','Creative'),(5,'Social Media Intern','Marketing'),(5,'Business Intern','Business'),
(6,'System Support Intern','IT'),(6,'Cybersecurity Intern','Security'),(6,'Project Management Intern','Projects');

INSERT IGNORE INTO users (name,email,password,role,company_id) VALUES
('Employer 1','hr1@interntrack.test','$2y$12$aE8iaJdolJvFENIhsyBOgurstZ.7LgE7easwvFs8uvPxj6pZL40aa','employer',1),
('Employer 2','hr2@interntrack.test','$2y$12$aE8iaJdolJvFENIhsyBOgurstZ.7LgE7easwvFs8uvPxj6pZL40aa','employer',2),
('Employer 3','hr3@interntrack.test','$2y$12$aE8iaJdolJvFENIhsyBOgurstZ.7LgE7easwvFs8uvPxj6pZL40aa','employer',3),
('Employer 4','hr4@interntrack.test','$2y$12$aE8iaJdolJvFENIhsyBOgurstZ.7LgE7easwvFs8uvPxj6pZL40aa','employer',4),
('Employer 5','hr5@interntrack.test','$2y$12$aE8iaJdolJvFENIhsyBOgurstZ.7LgE7easwvFs8uvPxj6pZL40aa','employer',5),
('Employer 6','hr6@interntrack.test','$2y$12$aE8iaJdolJvFENIhsyBOgurstZ.7LgE7easwvFs8uvPxj6pZL40aa','employer',6);
