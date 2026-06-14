@extends('admin.layouts.horizontal')

@section('title', 'System Guide')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-book-open me-2"></i>
                        School ERP System - Complete Guide
                        <br>
                        <small class="fs-6">स्कूल ERP सिस्टम - पूर्ण गाइड</small>
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Quick Navigation -->
                    <div class="alert alert-info">
                        <h5><i class="fas fa-compass me-2"></i>Quick Navigation | त्वरित नेविगेशन</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0">
                                    <li><a href="#overview" class="text-decoration-none">📚 System Overview</a></li>
                                    <li><a href="#modules" class="text-decoration-none">🔧 All Modules</a></li>
                                    <li><a href="#how-to-use" class="text-decoration-none">📖 How to Use</a></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0">
                                    <li><a href="#workflows" class="text-decoration-none">🔄 Step by Step Guide</a></li>
                                    <li><a href="#technical" class="text-decoration-none">⚙️ Technical Details</a></li>
                                    <li><a href="#support" class="text-decoration-none">🆘 Support</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- System Overview -->
                    <div id="overview" class="mb-5">
                        <h3 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            System Overview | सिस्टम अवलोकन
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5>What is this system? | यह सिस्टम क्या है?</h5>
                                <p><strong>English:</strong> This is a complete School ERP (Enterprise Resource Planning) system built with Laravel PHP framework and MySQL database. It manages all school operations including students, teachers, attendance, exams, fees, certificates, and more.</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Hindi:</strong> यह एक पूर्ण स्कूल ERP (एंटरप्राइज रिसोर्स प्लानिंग) सिस्टम है जो Laravel PHP फ्रेमवर्क और MySQL डेटाबेस के साथ बनाया गया है। यह छात्रों, शिक्षकों, उपस्थिति, परीक्षा, फीस, प्रमाणपत्र और अधिक सहित सभी स्कूल संचालन का प्रबंधन करता है।</p>
                            </div>
                        </div>

                        <div class="card bg-light mt-3">
                            <div class="card-body">
                                <h6>System Architecture | सिस्टम आर्किटेक्चर</h6>
                                <pre class="bg-white p-3 rounded">
┌─────────────────────────────────────────┐
│         Frontend (Views)                │
│  Bootstrap 5 + Font Awesome + Custom CSS│
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│      Backend (Laravel Controllers)      │
│    Business Logic + Validation          │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│       Models (Eloquent ORM)             │
│    Database Relationships               │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│      Database (MySQL)                   │
│    Data Storage + Queries               │
└─────────────────────────────────────────┘
                                </pre>
                            </div>
                        </div>
                    </div>

                    <!-- All Modules -->
                    <div id="modules" class="mb-5">
                        <h3 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-th-large text-success me-2"></i>
                            All Modules | सभी मॉड्यूल
                        </h3>

                        <!-- Module Cards -->
                        <div class="row g-3">
                            <!-- Admin Module -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Admin Module</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>English:</strong> Complete admin dashboard with statistics, user management, and system settings.</p>
                                        <p><strong>Hindi:</strong> सांख्यिकी, उपयोगकर्ता प्रबंधन और सिस्टम सेटिंग्स के साथ पूर्ण एडमिन डैशबोर्ड।</p>
                                        <h6>Features:</h6>
                                        <ul class="small">
                                            <li>Dashboard with real-time stats</li>
                                            <li>User management</li>
                                            <li>Role & Permission management</li>
                                            <li>System settings</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Management -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Student Management</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>English:</strong> Complete student lifecycle management from admission to pass out.</p>
                                        <p><strong>Hindi:</strong> प्रवेश से पास आउट तक पूर्ण छात्र जीवनचक्र प्रबंधन।</p>
                                        <h6>Features:</h6>
                                        <ul class="small">
                                            <li>Student admission</li>
                                            <li>Profile management</li>
                                            <li>Student promotion</li>
                                            <li>Strength reports</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance Module -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Attendance Module</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>English:</strong> Track daily attendance for students and staff.</p>
                                        <p><strong>Hindi:</strong> छात्रों और कर्मचारियों के लिए दैनिक उपस्थिति ट्रैक करें।</p>
                                        <h6>Status Options:</h6>
                                        <ul class="small">
                                            <li>Present (उपस्थित)</li>
                                            <li>Absent (अनुपस्थित)</li>
                                            <li>Leave (छुट्टी)</li>
                                            <li>Late (देर से)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Examination Module -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Examination Module</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>English:</strong> Complete examination management with marks entry and result generation.</p>
                                        <p><strong>Hindi:</strong> मार्क्स एंट्री और परिणाम जनरेशन के साथ पूर्ण परीक्षा प्रबंधन।</p>
                                        <h6>Features:</h6>
                                        <ul class="small">
                                            <li>Exam creation</li>
                                            <li>Marks entry</li>
                                            <li>Result calculation</li>
                                            <li>Report card PDF</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Fee Management -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Fee Management</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>English:</strong> Complete fee management with collection and receipt generation.</p>
                                        <p><strong>Hindi:</strong> संग्रह और रसीद जनरेशन के साथ पूर्ण फीस प्रबंधन।</p>
                                        <h6>Payment Modes:</h6>
                                        <ul class="small">
                                            <li>Cash, UPI, Card</li>
                                            <li>Cheque, Bank Transfer</li>
                                            <li>Online Payment</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Certificate Module -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0"><i class="fas fa-certificate me-2"></i>Certificate Module</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>English:</strong> Generate various certificates with QR code verification.</p>
                                        <p><strong>Hindi:</strong> QR कोड सत्यापन के साथ विभिन्न प्रमाणपत्र जनरेट करें।</p>
                                        <h6>Certificate Types:</h6>
                                        <ul class="small">
                                            <li>Bonafide</li>
                                            <li>Transfer</li>
                                            <li>Character</li>
                                            <li>Fee Clearance</li>
                                            <li>Migration</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- How to Use -->
                    <div id="how-to-use" class="mb-5">
                        <h3 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-book-reader text-info me-2"></i>
                            How to Use | उपयोग कैसे करें
                        </h3>

                        <div class="accordion" id="howToUseAccordion">
                            <!-- For Admin -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#adminGuide">
                                        <i class="fas fa-user-shield me-2"></i>For Admin | एडमिन के लिए
                                    </button>
                                </h2>
                                <div id="adminGuide" class="accordion-collapse collapse show" data-bs-parent="#howToUseAccordion">
                                    <div class="accordion-body">
                                        <h6>Step 1: Login | लॉगिन करें</h6>
                                        <div class="alert alert-secondary">
                                            <strong>URL:</strong> <code>http://127.0.0.1:8000/admin/login</code><br>
                                            <strong>Username:</strong> admin<br>
                                            <strong>Password:</strong> admin123
                                        </div>

                                        <h6>Step 2: Dashboard | डैशबोर्ड</h6>
                                        <ul>
                                            <li>View all statistics</li>
                                            <li>Check today's attendance</li>
                                            <li>See pending fees</li>
                                            <li>View recent activities</li>
                                        </ul>

                                        <h6>Step 3: Manage Students | छात्रों का प्रबंधन</h6>
                                        <ul>
                                            <li>Add new students</li>
                                            <li>Update student information</li>
                                            <li>Promote students to next class</li>
                                            <li>View student strength</li>
                                        </ul>

                                        <h6>Step 4: Mark Attendance | उपस्थिति चिह्नित करें</h6>
                                        <ul>
                                            <li>Go to Attendance menu</li>
                                            <li>Select class and date</li>
                                            <li>Mark Present/Absent/Leave/Late</li>
                                            <li>Save attendance</li>
                                        </ul>

                                        <h6>Step 5: Manage Exams | परीक्षा प्रबंधन</h6>
                                        <ul>
                                            <li>Create exam</li>
                                            <li>Add subjects</li>
                                            <li>Enter marks</li>
                                            <li>Generate results</li>
                                        </ul>

                                        <h6>Step 6: Collect Fees | फीस एकत्र करें</h6>
                                        <ul>
                                            <li>Go to Fees menu</li>
                                            <li>Select student</li>
                                            <li>Enter amount and payment mode</li>
                                            <li>Generate receipt</li>
                                        </ul>

                                        <h6>Step 7: Generate Certificates | प्रमाणपत्र जनरेट करें</h6>
                                        <ul>
                                            <li>Go to Certificates menu</li>
                                            <li>Select student and type</li>
                                            <li>Fill details</li>
                                            <li>Download PDF</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- For Students -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#studentGuide">
                                        <i class="fas fa-user-graduate me-2"></i>For Students | छात्रों के लिए
                                    </button>
                                </h2>
                                <div id="studentGuide" class="accordion-collapse collapse" data-bs-parent="#howToUseAccordion">
                                    <div class="accordion-body">
                                        <h6>Step 1: Login | लॉगिन करें</h6>
                                        <div class="alert alert-secondary">
                                            <strong>URL:</strong> <code>http://127.0.0.1:8000/student/login</code><br>
                                            <strong>Username:</strong> (your username)<br>
                                            <strong>Password:</strong> (your password)
                                        </div>

                                        <h6>Step 2: View Dashboard | डैशबोर्ड देखें</h6>
                                        <ul>
                                            <li>Check attendance percentage</li>
                                            <li>See pending assignments</li>
                                            <li>View fee status</li>
                                            <li>Check notifications</li>
                                        </ul>

                                        <h6>Step 3: Submit Assignment | असाइनमेंट जमा करें</h6>
                                        <ul>
                                            <li>Go to Assignments</li>
                                            <li>Click Submit button</li>
                                            <li>Upload file or enter text</li>
                                            <li>Submit</li>
                                        </ul>

                                        <h6>Step 4: View Results | परिणाम देखें</h6>
                                        <ul>
                                            <li>Go to Results</li>
                                            <li>View marks and grades</li>
                                            <li>Download report card</li>
                                        </ul>

                                        <h6>Step 5: Check Fees | फीस जांचें</h6>
                                        <ul>
                                            <li>Go to Fees</li>
                                            <li>View total, paid, due</li>
                                            <li>Download receipts</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step by Step Workflows -->
                    <div id="workflows" class="mb-5">
                        <h3 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-tasks text-warning me-2"></i>
                            Step by Step Guide | चरण दर चरण गाइड
                        </h3>

                        <div class="row">
                            <!-- Student Admission Workflow -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">1. Student Admission | छात्र प्रवेश</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6>English:</h6>
                                        <ol class="small">
                                            <li>Admin logs in</li>
                                            <li>Goes to Students → Student Admission</li>
                                            <li>Fills admission form</li>
                                            <li>Uploads documents</li>
                                            <li>Submits form</li>
                                            <li>Student is added to database</li>
                                            <li>Username and password are created</li>
                                        </ol>
                                        <h6>Hindi:</h6>
                                        <ol class="small">
                                            <li>एडमिन लॉगिन करता है</li>
                                            <li>Students → Student Admission पर जाता है</li>
                                            <li>प्रवेश फॉर्म भरता है</li>
                                            <li>दस्तावेज़ अपलोड करता है</li>
                                            <li>फॉर्म सबमिट करता है</li>
                                            <li>छात्र डेटाबेस में जोड़ा जाता है</li>
                                            <li>यूजरनेम और पासवर्ड बनाए जाते हैं</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <!-- Daily Attendance Workflow -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">2. Daily Attendance | दैनिक उपस्थिति</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6>English:</h6>
                                        <ol class="small">
                                            <li>Admin/Teacher logs in</li>
                                            <li>Goes to Attendance → Mark Attendance</li>
                                            <li>Selects class, section, and date</li>
                                            <li>Marks each student</li>
                                            <li>Saves attendance</li>
                                            <li>System calculates percentage</li>
                                            <li>Updates student dashboard</li>
                                        </ol>
                                        <h6>Hindi:</h6>
                                        <ol class="small">
                                            <li>एडमिन/शिक्षक लॉगिन करता है</li>
                                            <li>Attendance → Mark Attendance पर जाता है</li>
                                            <li>कक्षा, सेक्शन और तारीख चुनता है</li>
                                            <li>प्रत्येक छात्र को चिह्नित करता है</li>
                                            <li>उपस्थिति सहेजता है</li>
                                            <li>सिस्टम प्रतिशत की गणना करता है</li>
                                            <li>छात्र डैशबोर्ड अपडेट होता है</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <!-- Exam Process Workflow -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">3. Exam Process | परीक्षा प्रक्रिया</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6>English:</h6>
                                        <ol class="small">
                                            <li>Admin creates exam</li>
                                            <li>Adds subjects to exam</li>
                                            <li>Sets exam dates</li>
                                            <li>Conducts exam</li>
                                            <li>Teacher enters marks</li>
                                            <li>System calculates results</li>
                                            <li>Generates report cards</li>
                                            <li>Students can view and download</li>
                                        </ol>
                                        <h6>Hindi:</h6>
                                        <ol class="small">
                                            <li>एडमिन परीक्षा बनाता है</li>
                                            <li>परीक्षा में विषय जोड़ता है</li>
                                            <li>परीक्षा तिथियां सेट करता है</li>
                                            <li>परीक्षा आयोजित करता है</li>
                                            <li>शिक्षक मार्क्स दर्ज करता है</li>
                                            <li>सिस्टम परिणाम की गणना करता है</li>
                                            <li>रिपोर्ट कार्ड जनरेट करता है</li>
                                            <li>छात्र देख और डाउनलोड कर सकते हैं</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <!-- Fee Collection Workflow -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0">4. Fee Collection | फीस संग्रह</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6>English:</h6>
                                        <ol class="small">
                                            <li>Admin creates fee structure</li>
                                            <li>Assigns fees to students</li>
                                            <li>Student/Parent pays fee</li>
                                            <li>Admin collects fee</li>
                                            <li>Enters payment details</li>
                                            <li>System generates receipt</li>
                                            <li>Updates fee status</li>
                                            <li>Student can download receipt</li>
                                        </ol>
                                        <h6>Hindi:</h6>
                                        <ol class="small">
                                            <li>एडमिन फीस संरचना बनाता है</li>
                                            <li>छात्रों को फीस असाइन करता है</li>
                                            <li>छात्र/अभिभावक फीस देता है</li>
                                            <li>एडमिन फीस एकत्र करता है</li>
                                            <li>भुगतान विवरण दर्ज करता है</li>
                                            <li>सिस्टम रसीद जनरेट करता है</li>
                                            <li>फीस स्थिति अपडेट करता है</li>
                                            <li>छात्र रसीद डाउनलोड कर सकता है</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Details -->
                    <div id="technical" class="mb-5">
                        <h3 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-cogs text-secondary me-2"></i>
                            Technical Details | तकनीकी विवरण
                        </h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-dark text-white">
                                        <h5 class="mb-0">Technology Stack</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6>Backend:</h6>
                                        <ul>
                                            <li>PHP 8.2</li>
                                            <li>Laravel 11.48</li>
                                            <li>MySQL 8.0</li>
                                        </ul>
                                        <h6>Frontend:</h6>
                                        <ul>
                                            <li>HTML5, CSS3</li>
                                            <li>Bootstrap 5.3</li>
                                            <li>JavaScript</li>
                                            <li>Font Awesome 6.4</li>
                                        </ul>
                                        <h6>Libraries:</h6>
                                        <ul>
                                            <li>Laravel Eloquent ORM</li>
                                            <li>DomPDF</li>
                                            <li>SimpleSoftwareIO QR Code</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-dark text-white">
                                        <h5 class="mb-0">Database Structure</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Total Tables:</strong> 45+</p>
                                        <h6>Main Tables:</h6>
                                        <ul class="small">
                                            <li>students - Student information</li>
                                            <li>teachers - Teacher information</li>
                                            <li>classes - Class information</li>
                                            <li>student_attendance - Attendance records</li>
                                            <li>exams - Exam information</li>
                                            <li>student_marks - Marks records</li>
                                            <li>fee_payments - Payment records</li>
                                            <li>certificates - Certificate records</li>
                                            <li>assignments - Assignment records</li>
                                            <li>timetables - Timetable records</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0">Security Features | सुरक्षा सुविधाएं</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>English:</h6>
                                        <ul>
                                            <li>Password hashing (bcrypt)</li>
                                            <li>CSRF protection</li>
                                            <li>SQL injection prevention</li>
                                            <li>XSS protection</li>
                                            <li>Session management</li>
                                            <li>Role-based access control</li>
                                            <li>File upload validation</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Hindi:</h6>
                                        <ul>
                                            <li>पासवर्ड हैशिंग</li>
                                            <li>CSRF सुरक्षा</li>
                                            <li>SQL इंजेक्शन रोकथाम</li>
                                            <li>XSS सुरक्षा</li>
                                            <li>सत्र प्रबंधन</li>
                                            <li>भूमिका-आधारित पहुंच नियंत्रण</li>
                                            <li>फ़ाइल अपलोड सत्यापन</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Support -->
                    <div id="support" class="mb-5">
                        <h3 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-life-ring text-danger me-2"></i>
                            Support | सहायता
                        </h3>

                        <div class="accordion" id="supportAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#commonIssues">
                                        Common Issues | सामान्य समस्याएं
                                    </button>
                                </h2>
                                <div id="commonIssues" class="accordion-collapse collapse show" data-bs-parent="#supportAccordion">
                                    <div class="accordion-body">
                                        <div class="alert alert-warning">
                                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Issue 1: Login not working</h6>
                                            <p><strong>Solution:</strong></p>
                                            <ul>
                                                <li>Check username and password</li>
                                                <li>Clear browser cache</li>
                                                <li>Check internet connection</li>
                                            </ul>
                                        </div>

                                        <div class="alert alert-warning">
                                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Issue 2: Page not loading</h6>
                                            <p><strong>Solution:</strong></p>
                                            <ul>
                                                <li>Refresh page (Ctrl+F5)</li>
                                                <li>Clear cache</li>
                                                <li>Check server status</li>
                                            </ul>
                                        </div>

                                        <div class="alert alert-warning">
                                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Issue 3: Data not saving</h6>
                                            <p><strong>Solution:</strong></p>
                                            <ul>
                                                <li>Check all required fields</li>
                                                <li>Check file size limits</li>
                                                <li>Check internet connection</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#maintenance">
                                        Maintenance Tasks | रखरखाव कार्य
                                    </button>
                                </h2>
                                <div id="maintenance" class="accordion-collapse collapse" data-bs-parent="#supportAccordion">
                                    <div class="accordion-body">
                                        <h6>Daily | दैनिक:</h6>
                                        <ul>
                                            <li>Mark attendance</li>
                                            <li>Check notifications</li>
                                            <li>Monitor system performance</li>
                                        </ul>

                                        <h6>Weekly | साप्ताहिक:</h6>
                                        <ul>
                                            <li>Generate attendance reports</li>
                                            <li>Check fee collection</li>
                                            <li>Review pending assignments</li>
                                        </ul>

                                        <h6>Monthly | मासिक:</h6>
                                        <ul>
                                            <li>Generate monthly reports</li>
                                            <li>Database backup</li>
                                            <li>System updates</li>
                                        </ul>

                                        <h6>Yearly | वार्षिक:</h6>
                                        <ul>
                                            <li>Student promotion</li>
                                            <li>Session change</li>
                                            <li>Archive old data</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Version History -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5><i class="fas fa-history me-2"></i>Version History | संस्करण इतिहास</h5>
                            <h6>Version 1.0 (May 2026)</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Modules Added:</strong></p>
                                    <ul class="small">
                                        <li>✅ Admin Dashboard</li>
                                        <li>✅ Student Management</li>
                                        <li>✅ Attendance Module</li>
                                        <li>✅ Examination Module</li>
                                        <li>✅ Fee Management</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="small">
                                        <li>✅ Certificate Module</li>
                                        <li>✅ Assignment Module</li>
                                        <li>✅ Student Dashboard</li>
                                        <li>✅ Notification System</li>
                                        <li>✅ Timetable Module</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center mt-4 pt-4 border-top">
                        <p class="text-muted">
                            <strong>Last Updated:</strong> May 1, 2026 | 
                            <strong>Version:</strong> 1.0 | 
                            <strong>Developer:</strong> Pankaj Maurya |
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    pre {
        font-size: 0.85rem;
    }
    .accordion-button:not(.collapsed) {
        background-color: #e7f3ff;
        color: #0d6efd;
    }
</style>
@endpush
@endsection
