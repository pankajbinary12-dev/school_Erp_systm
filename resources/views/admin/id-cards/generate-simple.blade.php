<!DOCTYPE html>
<html>
<head>
    <title>Student ID Cards</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print { .btn { display: none; } .page-break { page-break-after: always; } body { background: white !important; } }
        body { font-family: Arial; padding: 20px; background: #f0f0f0; }
        .container { max-width: 1400px; margin: 0 auto; }
        .btn { padding: 12px 25px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 25px; cursor: pointer; font-size: 16px; font-weight: bold; margin-bottom: 20px; }
        .card-pair { display: inline-flex; gap: 20px; margin: 15px; vertical-align: top; }
        .card { width: 320px; height: 500px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: relative; overflow: hidden; }
        
        /* Wave Designs */
        .wave-top { position: absolute; top: 0; left: 0; width: 100%; height: 150px; background: linear-gradient(135deg, #0066cc, #ff9900); clip-path: path('M0,0 L320,0 L320,80 Q240,140 160,80 Q80,20 0,80 Z'); z-index: 1; }
        .wave-bottom { position: absolute; bottom: 0; left: 0; width: 100%; height: 180px; background: linear-gradient(135deg, #ff9900, #0066cc); clip-path: path('M0,180 L0,100 Q80,40 160,100 Q240,160 320,100 L320,180 Z'); z-index: 1; }
        
        .card-content { position: relative; z-index: 10; padding: 20px; text-align: center; }
        
        /* School Logo */
        .school-logo { width: 60px; height: 60px; margin: 10px auto 5px; object-fit: contain; }
        .school-name { font-size: 18px; font-weight: bold; color: #333; margin: 5px 0; line-height: 1.2; }
        .tagline { font-size: 11px; color: #666; margin-top: 5px; text-transform: uppercase; letter-spacing: 1px; }
        
        /* Photo */
        .photo-frame { width: 140px; height: 140px; border: 5px solid #0066cc; border-radius: 20px; margin: 15px auto 20px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .photo-frame img { width: 100%; height: 100%; object-fit: cover; }
        
        /* Name */
        .student-name { font-size: 22px; font-weight: bold; margin: 15px 0 5px; }
        .student-name .first { color: #ff9900; }
        .student-name .last { color: #0066cc; }
        .position { font-size: 13px; color: #666; margin-bottom: 15px; }
        
        /* Details List */
        .details-list { text-align: left; margin: 20px 30px; font-size: 14px; line-height: 2; }
        .details-list div { display: flex; }
        .details-list .label { color: #0066cc; font-weight: bold; width: 100px; }
        .details-list .value { color: #333; font-weight: 600; }
        
        /* Back Side */
        .card-back .card-content { padding: 30px 25px; }
        .back-logo-img { width: 50px; height: 50px; margin: 15px auto 10px; object-fit: contain; }
        .back-school-name { font-size: 16px; font-weight: bold; color: #333; margin: 10px 0; line-height: 1.2; }
        .back-info { text-align: left; margin: 25px 0; font-size: 13px; line-height: 2; color: #555; }
        .back-info div { margin: 8px 0; }
        .back-info .label { color: #0066cc; font-weight: 600; }
        .date-info { margin: 25px 0; font-size: 13px; }
        .date-info div { margin: 10px 0; }
        .date-info .joined { color: #00cc66; }
        .date-info .expire { color: #ff6600; }
        .signature { margin-top: 40px; text-align: center; font-size: 14px; color: #666; font-style: italic; }
        
        /* QR Code */
        .qr-code { width: 100px; height: 100px; margin: 20px auto; display: block; border: 3px solid #0066cc; border-radius: 10px; padding: 5px; background: white; }
    </style>
</head>
<body>
    @php
        $school = \App\Models\SchoolSetting::first();
    @endphp
    <div class="container">
        <button class="btn" onclick="window.print()"><i class="fas fa-print"></i> Print ID Cards</button>
        
        @foreach($students as $student)
        <div class="card-pair">
            <!-- FRONT SIDE -->
            <div class="card">
                <div class="wave-top"></div>
                <div class="wave-bottom"></div>
                <div class="card-content">
                    @if($school && $school->logo)
                        <img src="{{ asset('storage/'.$school->logo) }}" alt="Logo" class="school-logo">
                    @else
                        <div style="width: 60px; height: 60px; margin: 10px auto 5px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">
                            {{ substr($school->school_name ?? 'SCHOOL', 0, 1) }}
                        </div>
                    @endif
                    <div class="school-name">{{ $school->school_name ?? 'School Name' }}</div>
                    <div class="tagline">{{ $school->board ?? 'Education Board' }}</div>
                    
                    <div class="photo-frame">
                        <img src="{{ $student->photo ? asset('storage/'.$student->photo) : 'https://ui-avatars.com/api/?name='.urlencode($student->full_name).'&size=140&background=0066cc&color=fff' }}" alt="Photo">
                    </div>
                    
                    @php
                        $names = explode(' ', $student->full_name, 2);
                        $firstName = $names[0] ?? '';
                        $lastName = $names[1] ?? '';
                    @endphp
                    <div class="student-name">
                        <span class="first">{{ strtoupper($firstName) }}</span> 
                        <span class="last">{{ strtoupper($lastName) }}</span>
                    </div>
                    <div class="position">Student</div>
                    
                    <div class="details-list">
                        <div><span class="label">EMP ID</span><span class="value">: {{ $student->admission_no }}</span></div>
                        <div><span class="label">Blood</span><span class="value">: {{ $student->blood_group ?? 'O+' }}</span></div>
                        <div><span class="label">Mail</span><span class="value">: {{ $student->email ?? '0000000000' }}</span></div>
                        <div><span class="label">Phone</span><span class="value">: {{ $student->guardian_phone }}</span></div>
                    </div>
                </div>
            </div>
            
            <!-- BACK SIDE -->
            <div class="card card-back">
                <div class="wave-top"></div>
                <div class="wave-bottom"></div>
                <div class="card-content">
                    @if($school && $school->logo)
                        <img src="{{ asset('storage/'.$school->logo) }}" alt="Logo" class="back-logo-img">
                    @else
                        <div style="width: 50px; height: 50px; margin: 15px auto 10px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; font-weight: bold;">
                            {{ substr($school->school_name ?? 'S', 0, 1) }}
                        </div>
                    @endif
                    <div class="back-school-name">{{ $school->school_name ?? 'School Name' }}<br><span style="font-size:11px;">{{ $school->board ?? 'Education Board' }}</span></div>
                    
                    <div class="back-info">
                        <div><span class="label">Address :</span> {{ $school->address ?? 'School Address' }}</div>
                        <div><span class="label">E-mail :</span> {{ $school->email ?? 'school@email.com' }}</div>
                        <div><span class="label">Contact :</span> {{ $school->phone ?? '0000000000' }}</div>
                    </div>
                    
                    <div class="date-info">
                        <div class="joined"><strong>Joined Date :</strong> {{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d/m/Y') : 'DD/MM/YEAR' }}</div>
                        <div class="expire"><strong>Expire Date :</strong> {{ date('d/m/Y', strtotime('+1 year')) }}</div>
                    </div>
                    
                    @if($template->show_qr_code)
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($student->admission_no) }}" class="qr-code" alt="QR">
                    @endif
                    
                    <div class="signature">
                        @if($school && $school->principal_signature)
                            <img src="{{ asset('storage/'.$school->principal_signature) }}" style="width: 100px; height: auto; margin-bottom: 5px;">
                        @endif
                        <div>{{ $school->principal_name ?? 'Principal' }}</div>
                        <div style="font-size: 11px; color: #999;">Principal Signature</div>
                    </div>
                </div>
            </div>
        </div>
        @if($loop->iteration % 2 == 0)<div class="page-break"></div>@endif
        @endforeach
    </div>
</body>
</html>
