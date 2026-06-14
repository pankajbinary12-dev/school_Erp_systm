<!DOCTYPE html>
<html>
<head>
    <title>Student ID Cards - {{ $template->template_name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<style>
@media print{@page{margin:0;size:A4}body{margin:0.5cm}.no-print{display:none!important}.page-break{page-break-after:always}}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,sans-serif;background:#f0f0f0;padding:20px}
.container{max-width:1400px;margin:0 auto}
.print-btn{position:fixed;top:20px;right:20px;padding:12px 25px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;border-radius:25px;cursor:pointer;font-size:16px;font-weight:bold;z-index:1000;box-shadow:0 5px 15px rgba(0,0,0,0.3)}
.card-pair{display:inline-flex;gap:20px;margin:15px;vertical-align:top}
.id-card{width:320px;height:480px;background:white;border-radius:20px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.2);position:relative}
.card-content{position:relative;z-index:10;padding:25px;height:100%;display:flex;flex-direction:column;align-items:center}
.school-logo{width:70px;height:70px;background:#ffa500;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-top:10px;box-shadow:0 5px 15px rgba(0,0,0,0.2)}
.school-logo i{font-size:35px;color:white}
.school-name{font-size:22px;font-weight:bold;margin-top:15px;text-align:center;text-transform:uppercase;letter-spacing:1px;text-shadow:2px 2px 4px rgba(0,0,0,0.3)}
.card-type{font-size:11px;text-transform:uppercase;letter-spacing:2px;margin-top:5px;text-shadow:1px 1px 3px rgba(0,0,0,0.3)}
.student-photo{width:110px;height:110px;border-radius:12px;border:4px solid #ffa500;object-fit:cover;margin-top:25px;box-shadow:0 5px 15px rgba(0,0,0,0.2)}
.student-id-badge{background:#ffa500;color:white;padding:8px 20px;border-radius:20px;font-size:13px;font-weight:bold;margin-top:15px;box-shadow:0 3px 10px rgba(0,0,0,0.2)}
.student-name{font-size:18px;font-weight:bold;color:#333;margin-top:20px;text-align:center;text-transform:uppercase}
.student-details{width:100%;margin-top:15px;font-size:13px;color:#555}
.detail-row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #eee}
.detail-label{font-weight:600;color:#17a2b8}
.detail-value{font-weight:600;text-align:right}
.wave-top{position:absolute;top:0;left:0;width:100%;height:180px;background:linear-gradient(135deg,#17a2b8,#ffa500);clip-path:ellipse(100% 100% at 50% 0%);z-index:1}
.wave-bottom{position:absolute;bottom:0;left:0;width:100%;height:150px;background:linear-gradient(135deg,#ffa500,#17a2b8);clip-path:ellipse(100% 100% at 50% 100%);z-index:1}
.template-wave .school-name,.template-wave .card-type{color:white}
.id-card-back{background:linear-gradient(135deg,#667eea,#764ba2)}
.back-content{position:relative;z-index:10;padding:30px 25px;height:100%;display:flex;flex-direction:column;color:white}
.back-header{text-align:center;font-size:16px;font-weight:bold;margin-bottom:20px}
.contact-box{background:rgba(255,255,255,0.2);padding:15px;border-radius:12px;margin-bottom:15px;backdrop-filter:blur(10px)}
.contact-box h4{font-size:14px;margin-bottom:10px}
.contact-box p{font-size:12px;margin:5px 0}
.signature-section{display:flex;justify-content:space-between;margin-top:15px;gap:15px}
.signature-line{border-top:2px solid rgba(255,255,255,0.5);padding-top:5px;font-size:10px;margin-top:25px}
.qr-section{text-align:center;margin-top:auto;padding:15px;background:rgba(255,255,255,0.95);border-radius:12px}
.qr-code{width:120px;height:120px;margin:10px auto;display:block;border-radius:8px;background:white;padding:5px}
.qr-label{font-size:11px;color:#333;font-weight:600}
.validity{text-align:center;font-size:11px;margin-top:10px}
</style>

<div class="container">
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print ID Cards</button>
    
    @foreach($students as $student)
    <div class="card-pair">
        <!-- FRONT SIDE -->
        <div class="id-card template-{{ $template->border_style }}">
            @if($template->border_style == 'wave')
            <div class="wave-top"></div>
            <div class="wave-bottom"></div>
            @endif
            
            <div class="card-content">
                @if($template->show_logo)
                <div class="school-logo"><i class="fas fa-graduation-cap"></i></div>
                @endif
                
                <div class="school-name">SCHOOL NAME</div>
                <div class="card-type">Student Identity Card</div>
                
                <img src="{{ $student->photo ? asset('storage/'.$student->photo) : 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&size=110&background=17a2b8&color=fff' }}" 
                     class="student-photo" alt="Photo">
                
                <div class="student-id-badge">
                    <i class="fas fa-id-badge"></i> {{ $student->admission_no }}
                </div>
                
                <div class="student-name">{{ $student->full_name }}</div>
                
                <div class="student-details">
                    <div class="detail-row">
                        <span class="detail-label">Father's Name:</span>
                        <span class="detail-value">{{ $student->father_name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Class:</span>
                        <span class="detail-value">{{ $student->class->class_name }} - {{ $student->section->section_name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Roll No:</span>
                        <span class="detail-value">{{ $student->roll_no ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">DOB:</span>
                        <span class="detail-value">{{ $student->date_of_birth->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- BACK SIDE -->
        <div class="id-card id-card-back">
            <div class="wave-top"></div>
            <div class="wave-bottom"></div>
            
            <div class="back-content">
                <div class="back-header">
                    <i class="fas fa-phone-alt"></i> EMERGENCY CONTACT
                </div>
                
                <div class="contact-box">
                    <h4><i class="fas fa-user-shield"></i> Guardian Contact</h4>
                    <p><strong>Phone:</strong> {{ $student->guardian_phone }}</p>
                    @if($student->email)
                    <p><strong>Email:</strong> {{ $student->email }}</p>
                    @endif
                    <p><strong>Address:</strong> {{ $student->address ?? 'Not provided' }}</p>
                </div>
                
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line">Student Signature</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line">Principal Signature</div>
                    </div>
                </div>
                
                @if($template->show_qr_code)
                <div class="qr-section">
                    <div class="qr-label">Scan for Verification</div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($student->admission_no) }}" 
                         class="qr-code" alt="QR Code">
                    <div class="validity">Valid {{ date('Y') }}-{{ date('Y')+1 }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    @if($loop->iteration % 4 == 0)
    <div class="page-break"></div>
    @endif
    @endforeach
</div>
</body>
</html>
