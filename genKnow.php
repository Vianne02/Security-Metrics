<?php
require_once('vendor/autoload.php'); // Include TCPDF library
require_once('config.php'); // This includes your database connection

// Extend TCPDF to create custom header and footer
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        // Set font
        $this->SetFont('helvetica', 'B', 12);
        // Title
        $this->Cell(0, 10, 'Knowledge Retention Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
$pdf->SetTitle('Knowledge Retention Report');
$pdf->SetSubject('Report');
$pdf->SetKeywords('TCPDF, PDF, Knowledge Retention, Report');

// Set default header data
$pdf->SetHeaderData('', 0, 'Knowledge Retention Report', '');

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

// Content
$html = '<h1>Knowledge Retention Report</h1>';

// Get the latest performance
$query_latest_performance = "SELECT performance FROM knowledge ORDER BY date_published DESC LIMIT 1";
$latest_performance = $link->query($query_latest_performance)->fetch_assoc()['performance'];

// Color code for each performance category
$performance_colors = array(
    'Improved' => 'green',
    'Depreciated' => 'red',
    'Stagnant' => 'orange'
);

// Add the latest performance in color
$html .= '<p style="color: ' . $performance_colors[$latest_performance] . '; font-weight: bold;">Latest Performance: ' . $latest_performance . '</p>';

$html .= '<table border="1" cellspacing="0" cellpadding="5">';
$html .= '<tr>';
$html .= '<th>Knowledge Before</th>';
$html .= '<th>Knowledge After</th>';
$html .= '<th>Result</th>';
$html .= '<th>Performance</th>';
$html .= '<th>Date Published</th>';
$html .= '</tr>';

$query = "SELECT knowledge_before, knowledge_after, result, performance, date_published FROM knowledge ORDER BY date_published DESC";
if ($result = $link->query($query)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . $row['knowledge_before'] . '</td>';
            $html .= '<td>' . $row['knowledge_after'] . '</td>';
            $html .= '<td>' . $row['result'] . '</td>';
            // Apply color based on performance
            $html .= '<td style="color: ' . $performance_colors[$row['performance']] . ';">' . $row['performance'] . '</td>';
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
$pdf->Output('knowledge_retention_report.pdf', 'I');
?>
