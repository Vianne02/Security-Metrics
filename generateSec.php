<?php
require_once 'vendor/autoload.php';

// Database connection
$hostname = "localhost"; // Change this to your database hostname
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$database = "healthcare_security"; // Change this to your database name

$link = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}

// Extend TCPDF to create custom header and footer
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        // Set font
        $this->SetFont('helvetica', 'B', 12);
        // Title
        $this->Cell(0, 10, 'Security Incidents Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        // Line break
        $this->Ln(10);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Create new PDF document
$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Security Incidents Report');
$pdf->SetSubject('Report');
$pdf->SetKeywords('TCPDF, PDF, Security Incidents, Report');

// Set default header data
$pdf->SetHeaderData('', 0, 'Security Incidents Report', '');

// Set header and footer fonts
$pdf->setHeaderFont(Array('helvetica', '', 12));
$pdf->setFooterFont(Array('helvetica', '', 8));

// Set margins
$pdf->SetMargins(15, 25, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font
$pdf->SetFont('helvetica', '', 10);

// Add a page
$pdf->AddPage();

// Function to add a styled header for performance change
function addPerformanceChangeHeader($pdf, $performanceType) {
    switch ($performanceType) {
        case 'Improved':
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->SetTextColor(0, 128, 0); // Green color for improved performance
            $pdf->Cell(0, 10, 'Performance Improvement Detected!', 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 12);
            $pdf->SetTextColor(0, 0, 0); // Reset text color
            $pdf->MultiCell(0, 10, "The latest report indicates a significant improvement in performance. This is a positive development for our security measures. It shows that our efforts and strategies are effective and contributing to a safer environment.", 0, 'J');
            break;
        case 'Reduced':
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->SetTextColor(255, 0, 0); // Red color for reduced performance
            $pdf->Cell(0, 10, 'Performance Reduction Detected!', 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 12);
            $pdf->SetTextColor(0, 0, 0); // Reset text color
            $pdf->MultiCell(0, 10, "The latest report indicates a reduction in performance. This is a concerning trend that requires immediate attention. We need to analyze the underlying causes and take corrective actions to address the issues and improve our security posture.", 0, 'J');
            break;
        case 'Stagnant':
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->SetTextColor(255, 165, 0); // Orange color for stagnant performance
            $pdf->Cell(0, 10, 'Stagnant Performance Detected!', 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 12);
            $pdf->SetTextColor(0, 0, 0); // Reset text color
            $pdf->MultiCell(0, 10, "The latest report indicates stagnant performance. While there haven't been significant changes, it's essential to continuously monitor and improve our security practices. We should explore new strategies and technologies to stay ahead of potential threats.", 0, 'J');
            break;
    }
}

// Check the latest performance
$query_latest_performance = "SELECT performance FROM security_incidents ORDER BY date_published DESC LIMIT 1";
$latest_performance = $link->query($query_latest_performance)->fetch_assoc()['performance'];

// Add header based on the latest performance
addPerformanceChangeHeader($pdf, $latest_performance);

// Content
$html = '<h1>Security Incidents Data</h1>';
$html .= '<table border="1" cellspacing="0" cellpadding="5">';
$html .= '<tr>';
$html .= '<th>Last Training</th>';
$html .= '<th>Current Training</th>';
$html .= '<th>Result</th>';
$html .= '<th>Performance</th>';
$html .= '<th>Date Published</th>';
$html .= '</tr>';

$query = "SELECT security_last, security_current, result, performance, date_published FROM security_incidents ORDER BY date_published DESC";
if ($result = $link->query($query)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . $row['security_last'] . '</td>';
            $html .= '<td>' . $row['security_current'] . '</td>';
            $html .= '<td>' . $row['result'] . '</td>';
            // Apply color based on performance
            switch ($row['performance']) {
                case 'Improved':
                    $html .= '<td style="color: green;">' . $row['performance'] . '</td>';
                    break;
                case 'Reduced':
                    $html .= '<td style="color: red;">' . $row['performance'] . '</td>';
                    break;
                case 'Stagnant':
                    $html .= '<td style="color: orange;">' . $row['performance'] . '</td>';
                    break;
                default:
                    $html .= '<td>' . $row['performance'] . '</td>';
            }
            $html .= '<td>' . $row['date_published'] . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= '<tr><td colspan="5">No data found</td></tr>';
    }
    $result->close();
} else {
    $html .= '<tr><td colspan="5">Error fetching data</td></tr>';
}

$html .= '</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('security_incidents_report.pdf', 'I');

?>
