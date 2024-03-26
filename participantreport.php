<?php
require_once('vendor/autoload.php'); // Include TCPDF library
require_once('config.php'); // This includes your database connection

// Extend TCPDF to create custom header and footer
class MYPDF extends TCPDF {
    // Latest performance
    private $latestPerformance;

    // Set latest performance
    public function setLatestPerformance($performance) {
        $this->latestPerformance = $performance;
    }

    // Page header
    public function Header() {
        // Set font
        $this->SetFont('helvetica', 'B', 12);
        // Title
        $this->Cell(0, 10, 'Training Participant Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        // Line break
        $this->Ln(10);

        // Display latest performance text
        $this->Cell(0, 10, 'Latest Performance: ' . $this->latestPerformance, 0, false, 'C', 0, '', 0, false, 'T', 'M');
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
$pdf->SetTitle('Training Participant Report');
$pdf->SetSubject('Report');
$pdf->SetKeywords('TCPDF, PDF, Training Participant, Report');

// Set default header data
$pdf->SetHeaderData('', 0, 'Training Participant Report', '');

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

// Fetch latest performance from the database
$query = "SELECT result FROM training_participants ORDER BY date_published DESC LIMIT 1";
$result = $link->query($query);

// Check if the query executed successfully
if ($result === false) {
    die('Error: ' . $link->error); // Output error message and terminate script execution
}

// Get the latest performance
$latestPerformance = 'No data found';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latestPerformance = $row['result'];
}

// Set latest performance in the PDF
$pdf->setLatestPerformance($latestPerformance);

// Add a page
$pdf->AddPage();

// Start HTML content for the PDF
$html = '<h1>Training Participant Data</h1>';

// Start table
$html .= '<table border="1" cellspacing="0" cellpadding="5">';
$html .= '<tr>';
$html .= '<th>Last Training</th>';
$html .= '<th>Current Training</th>';
$html .= '<th>Result</th>';
$html .= '<th>Performance</th>';
$html .= '<th>Date Published</th>';
$html .= '</tr>';

// Populate table with data
$query = "SELECT participants_last, participants_current, result, date_published FROM training_participants ORDER BY date_published DESC";
$result = $link->query($query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td>' . $row['participants_last'] . '</td>';
        $html .= '<td>' . $row['participants_current'] . '</td>';
        $html .= '<td>' . $row['result'] . '</td>';
        // Determine performance category based on the result
        if ($row['result'] > 0) {
            $performance = "Improved";
            $color = 'green'; // Green for improved performance
        } elseif ($row['result'] == 0) {
            $performance = "Stagnant";
            $color = 'orange'; // Orange for stagnant performance
        } else {
            $performance = "Reduced";
            $color = 'red'; // Red for reduced performance
        }
        $html .= '<td style="color: ' . $color . ';">' . $performance . '</td>';
        $html .= '<td>' . $row['date_published'] . '</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="5">No data found</td></tr>';
}

// End table
$html .= '</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('training_participant_report.pdf', 'I');
?>
