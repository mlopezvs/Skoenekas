<?php
defined('_JEXEC') or die;

/**
 * Banners component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_dnagifts
 * @since		1.6
 */
class ReportsHelper
{
	public static function generateReportPDF($displaytype, $svgData, $imgChartSRC, $userTestID)
	{
		$author             = JText::_( 'COM_DNAGIFTS_PDF_AUTHOR' );
        $title              = JText::_( 'COM_DNAGIFTS_PDF_TITLE' );
		$subject            = JText::_( 'COM_DNAGIFTS_PDF_SUBJECT' );
        $keywords           = JText::_( 'COM_DNAGIFTS_PDF_KEYWORDS' );
		$documentname       = JText::_( 'COM_DNAGIFTS_PDF_FILENAME' );
        $bannerImage        = JText::_( 'COM_DNAGIFTS_PDF_BANNERIMAGE' );
        $bannerTitle        = JText::_( 'COM_DNAGIFTS_PDF_BANNERTITLE' );
        $bannerText         = JText::_( 'COM_DNAGIFTS_PDF_BANNERTEXT' );
        $bannerImageWidth   = 100;
        $html               = '';
        
		$report				= new DnaGiftsControllerReport();
		$model				= $report->getModel('Report', 'DnaGiftsModel');
		$dnaResults			= $model->getResultsObject($userTestID);
		
		// Generate the report's document name
        $user = JFactory::getUser();
		$user_id = $user->get("id");
        
        $db = JFactory::getDbo();
		$query = $db->getQuery(true);
        $query->select('test_id, started_datetime');
		$query->from($db->quoteName('#__dnagifts_lnk_user_tests'));
		$query->where('id = '.$userTestID);
        $db->setQuery($query);
        $result = $db->loadObject();
        $timeblah = array('-',':',' ');
        $timestamp = str_replace($timeblah, "", $result->started_datetime);
        
        $documentname = $documentname." (".$user_id."-".$timestamp."-".$result->test_id.")".".pdf";
        
		// Log the report name in next to the user-test record
		$query = $db->getQuery(true);
		$query->update('#__dnagifts_lnk_user_tests');
		$query->set('report_name = '.$db->quote($documentname));
		$query->where('id = ' . (int) $userTestID);
		$db->setQuery($query);
		$db->query();
		
		// Generate the PDF
        @ob_end_clean();      
        
        require_once(JPATH_ROOT.DS.'tcpdf'.DS.'config/lang/eng.php');
        require_once(JPATH_ROOT.DS.'tcpdf'.DS.'tcpdf.php');
        
		// ----------------- document setup ---------------------
		
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // set document information
        $pdf->SetAuthor($author);
        $pdf->SetTitle($title);
        $pdf->SetSubject($subject);
        $pdf->SetKeywords($keywords);
        
        // set default header data
        $pdf->SetHeaderData($bannerImage, $bannerImageWidth, $bannerTitle, $bannerText);
        
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        //set some language-dependent strings
        $pdf->setLanguageArray($l);
        
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 14, '', true);
        
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();
        
		// ----------------- end document setup ---------------------
		
		$linestyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => '2,1', 'phase' => 0, 'color' => array(211, 211, 211));
		// Line: Left-start, Top-start, Left-stop, Top-stop
		$pdf->Line(1, 30, 200, 30, $linestyle);
		$pdf->Line(15, 15, 15, 280, $linestyle);
		$pdf->Line(115, 15, 115, 280, $linestyle);
		$pdf->Line(120, 15, 120, 280, $linestyle);
		$pdf->Line(195, 15, 195, 280, $linestyle);
		
		$pdf->SetXY(15, 30);
		$pdf->Write(0, 'Hi XXXXXXXXXXXXX', '', 0, 'L', true, 0, false, false, 0);

		//$y = $pdf->GetY();
		//$pdf->SetY($y + 5);
		
		
		// TEXT REPLACEMENT VARIABLES
		$COM_DNAGIFTS_REPORT_HEREYOURESULTS	= JText::_('COM_DNAGIFTS_REPORT_HEREYOURESULTS');
		$COM_DNAGIFTS_REPORT_INTRO_P1		= JText::_('COM_DNAGIFTS_REPORT_INTRO_P1');
		$COM_DNAGIFTS_REPORT_INTRO_P2		= JText::_('COM_DNAGIFTS_REPORT_INTRO_P2');
		$COM_DNAGIFTS_REPORT_THGIFT			= JText::_('COM_DNAGIFTS_REPORT_THGIFT');
		$COM_DNAGIFTS_REPORT_THSCORE		= JText::_('COM_DNAGIFTS_REPORT_THSCORE');
		$COM_DNAGIFTS_REPORT_THYOURGIFT		= JText::_('COM_DNAGIFTS_REPORT_THYOURGIFT');
		
		$html = <<<EOD
		<table border="0" width="620" cellspacing="3" cellpadding="0" style="font-size:10pt">
			<tr>
				<td width="350">
					<p style="font-size: 14pt">$COM_DNAGIFTS_REPORT_HEREYOURESULTS</p>
					<p>$COM_DNAGIFTS_REPORT_INTRO_P1</p>
					<p>$COM_DNAGIFTS_REPORT_INTRO_P2</p>
				</td>
				<td width="15">&nbsp;</td>
				<td width="255">
				
				<table width="255" cellspacing="3" cellpadding="3" id="tblScores" style="border: 1px solid #c5c5c5;">
					<tr style="background-color: #000000; color: #ffffff; text-align: center;">
						<td width="75">$COM_DNAGIFTS_REPORT_THGIFT</td>
						<td width="75">$COM_DNAGIFTS_REPORT_THSCORE</td>
						<td>$COM_DNAGIFTS_REPORT_THYOURGIFT</td>
					</tr>
EOD;

		foreach($dnaResults as $data):
			$tdcolor = '';
			if ( in_array($data['abbr'], array('R','M')) ):
				$tdcolor = 'color: #ffffff;';
			endif;
			$html .= '<tr style="text-align: center; color: #333333; background-color: #'.$data['redColor'].';">'.
					'<td style="'.$tdcolor.'">'.$data['abbr'].'</td>'.
					'<td style="background-color: LightGrey;">'.$data['score'].'</td>'.
					'<td style="text-align: left;'.$tdcolor.'">'.$data['label'].'</td>'.
				'</tr>';
		endforeach;
		
		$html .= '</table></td></tr></table>';
        
		// Print text using writeHTML()
        $pdf->writeHTML($html);
		
		$y = $pdf->GetY();
		$pdf->Line(1, $y, 200, $y, $linestyle); // draw a horizontal line at the current position (height)
		
		
		// TEXT REPLACEMENT VARIABLES
		$COM_DNAGIFTS_REPORT_YOURLINEPROFILE	= JText::_('COM_DNAGIFTS_REPORT_YOURLINEPROFILE');
		$COM_DNAGIFTS_REPORT_DNACHART			= JText::_('COM_DNAGIFTS_REPORT_DNACHART');
		$COM_DNAGIFTS_REPORT_DNACHART_P1		= JText::_('COM_DNAGIFTS_REPORT_DNACHART_P1');
		$COM_DNAGIFTS_REPORT_DNACHART_P2		= JText::_('COM_DNAGIFTS_REPORT_DNACHART_P2');
		$COM_DNAGIFTS_REPORT_DNACHART_P3		= JText::_('COM_DNAGIFTS_REPORT_DNACHART_P3');
		$COM_DNAGIFTS_REPORT_DNACHART_P4		= JText::_('COM_DNAGIFTS_REPORT_DNACHART_P4');
		$COM_DNAGIFTS_REPORT_DNACHART_P5		= JText::_('COM_DNAGIFTS_REPORT_DNACHART_P5');
		$primsecimg = '<img src="'.JURI::base(true).'/media/com_dnagifts/images/primary-secondary-'.JText::_('COM_DNAGIFTS_REPORT_DNACHART_PRIMSECIMG').'-2.png" />';
		$html = <<<EOD
		<table border="0" width="620" cellspacing="3" cellpadding="0" style="font-size:10pt">
			<tr>
				<td colspan="3">
					<p style="font-size: 14pt">$COM_DNAGIFTS_REPORT_YOURLINEPROFILE</p>
				</td>
			</tr>
			<tr>
				<td width="350">
					<table id="tblDNAChart">
						<tr>
							<td align="center">
								<strong>$COM_DNAGIFTS_REPORT_DNACHART</strong>
							</td>
						</tr>
						<tr>	
							<td>
								<img src="$imgChartSRC" />
								$primsecimg
							</td>
						</tr>
					</table>
				</td>
				<td width="15">&nbsp;</td>
				<td width="255">
					<p>$COM_DNAGIFTS_REPORT_DNACHART_P1</p>
					<p>$COM_DNAGIFTS_REPORT_DNACHART_P2</p>
					<p>$COM_DNAGIFTS_REPORT_DNACHART_P3</p>
					<p>$COM_DNAGIFTS_REPORT_DNACHART_P4</p>
					<p>$COM_DNAGIFTS_REPORT_DNACHART_P5</p>
				</td>
			</tr>	
		</table>	
EOD;

		// Print text using writeHTML()
        $pdf->writeHTML($html);
		
		$y = $pdf->GetY();
		$pdf->Line(1, $y, 200, $y, $linestyle); // draw a horizontal line at the current position (height)

		// TEXT REPLACEMENT VARIABLES
		$COM_DNAGIFTS_REPORT_DNACOMP	= JText::_('COM_DNAGIFTS_REPORT_DNACOMP');
		$COM_DNAGIFTS_REPORT_DNACOMP_P1	= JText::_('COM_DNAGIFTS_REPORT_DNACOMP_P1');
		$COM_DNAGIFTS_REPORT_DNACOMP_P2	= JText::_('COM_DNAGIFTS_REPORT_DNACOMP_P2');
		$html = <<<EOD
		<table border="0" width="620" cellspacing="3" cellpadding="0" style="font-size:10pt">
			<tr>
				<td colspan="3"><p class="rptText16">$COM_DNAGIFTS_REPORT_DNACOMP</p></td>
			</tr>
			<tr>
				<td>

				</td>
				<td>&nbsp;</td>
				<td>
					<p>$COM_DNAGIFTS_REPORT_DNACOMP_P1</p>
					<p>$COM_DNAGIFTS_REPORT_DNACOMP_P2</p>
				</td>
			</tr>
EOD;

		// Print text using writeHTML()
        $pdf->writeHTML($html);
		
		$pdf->ImageSVG($file='@'.htmlspecialchars_decode($svgData['piechart_div']), $x='', $y=$pdf->GetY() - 50, $w='', $h=75, $link='', $align='', $palign='', $border=0, $fitonpage=false);
		
		$y = $pdf->GetY();
		$pdf->Line(1, $y, 200, $y, $linestyle); // draw a horizontal line at the current position (height)
				
		
		
        // Print text using writeHTMLCell()
        //$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
            
    
        // ---------------------------------------------------------
        
		//$pdf->Image($imgChartSRC, '', '', 100);
		
        // ---------------------------------------------------------
        
    	////$pdf->ImageSVG($file='@<svg width="400" height="300" style="overflow: hidden;"><defs id="defs"/><rect x="0" y="0" width="400" height="300" stroke="none" stroke-width="0" fill="#ffffff"/><g><text text-anchor="start" x="77" y="38.35" font-family="Arial" font-size="11" font-weight="bold" stroke="none" stroke-width="0" fill="#000000">How Much Pizza I Ate Last Night</text></g><g><rect x="248" y="58" width="76" height="83" stroke="none" stroke-width="0" fill-opacity="0" fill="#ffffff"/><g><rect x="248" y="58" width="76" height="11" stroke="none" stroke-width="0" fill-opacity="0" fill="#ffffff"/><g><text text-anchor="start" x="263" y="67.35" font-family="Arial" font-size="11" stroke="none" stroke-width="0" fill="#222222">Mushrooms</text></g><rect x="248" y="58" width="11" height="11" stroke="none" stroke-width="0" fill="#3366cc"/></g><g><rect x="248" y="76" width="76" height="11" stroke="none" stroke-width="0" fill-opacity="0" fill="#ffffff"/><g><text text-anchor="start" x="263" y="85.35" font-family="Arial" font-size="11" stroke="none" stroke-width="0" fill="#222222">Onions</text></g><rect x="248" y="76" width="11" height="11" stroke="none" stroke-width="0" fill="#dc3912"/></g><g><rect x="248" y="94" width="76" height="11" stroke="none" stroke-width="0" fill-opacity="0" fill="#ffffff"/><g><text text-anchor="start" x="263" y="103.35" font-family="Arial" font-size="11" stroke="none" stroke-width="0" fill="#222222">Olives</text></g><rect x="248" y="94" width="11" height="11" stroke="none" stroke-width="0" fill="#ff9900"/></g><g><rect x="248" y="112" width="76" height="11" stroke="none" stroke-width="0" fill-opacity="0" fill="#ffffff"/><g><text text-anchor="start" x="263" y="121.35" font-family="Arial" font-size="11" stroke="none" stroke-width="0" fill="#222222">Zucchini</text></g><rect x="248" y="112" width="11" height="11" stroke="none" stroke-width="0" fill="#109618"/></g><g><rect x="248" y="130" width="76" height="11" stroke="none" stroke-width="0" fill-opacity="0" fill="#ffffff"/><g><text text-anchor="start" x="263" y="139.35" font-family="Arial" font-size="11" stroke="none" stroke-width="0" fill="#222222">Pepperoni</text></g><rect x="248" y="130" width="11" height="11" stroke="none" stroke-width="0" fill="#990099"/></g></g><g><path d="M154,151L154,75A76,76,0,0,1,207.74011537017762,204.7401153701776L154,151A0,0,0,0,0,154,151" stroke="#ffffff" stroke-width="1" fill="#3366cc"/><text text-anchor="start" x="183.37241046222232" y="136.26323901017514" font-family="Arial" font-size="11" stroke="none" stroke-width="0" fill="#ffffff">37.5%</text></g><g><path d="M154,151L78,151A76,76,0,0,1,154,75L154,151A0,0,0,0,0,154,151" stroke="#ffffff" stroke-width="1" fill="#990099"/><text text-anchor="start" x="105.37040213776933" y="117.22040213776936" font-family="Arial" font-size="11" stroke="none" stroke-width="0" fill="#ffffff">25%</text></g><g><path d="M154,151L100.2598846298224,204.74011537017762A76,76,0,0,1,78,151L154,151A0,0,0,0,0,154,151" stroke="#ffffff" stroke-width="1" fill="#109618"/><text text-anchor="start" x="93.62758953777768" y="173.43676098982488" font-family="Arial" font-size="11" stroke="none" stroke-width="0" fill="#ffffff">12.5%</text></g><g><path d="M154,151L154,227A76,76,0,0,1,100.2598846298224,204.74011537017762L154,151A0,0,0,0,0,154,151" stroke="#ffffff" stroke-width="1" fill="#ff9900"/><text text-anchor="start" x="118.28323932673234" y="203.65757780465376" font-family="Arial" font-size="11" stroke="none" stroke-width="0" fill="#ffffff">12.5%</text></g><g><path d="M154,151L207.74011537017762,204.7401153701776A76,76,0,0,1,154,227L154,151A0,0,0,0,0,154,151" stroke="#ffffff" stroke-width="1" fill="#dc3912"/><text text-anchor="start" x="158.71676067326766" y="203.65757780465373" font-family="Arial" font-size="11" stroke="none" stroke-width="0" fill="#ffffff">12.5%</text></g><g/></svg>', $x=30, $y=100, $w='', $h=100, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        //$pdf->ImageSVG($file='@'.htmlspecialchars_decode($svgData['linechart_div']), $x='', $y='', $w='', $h=200, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        //$pdf->ImageSVG($file='@'.htmlspecialchars_decode($svgData['piechart_div']), $x='', $y='', $w='', $h=100, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        
		
		
		
        // ------------- write the document to disk -------------------
        if ($displaytype == 'F') {
            $filename = JPATH_SITE.DS."components".DS."com_dnagifts".DS."store".DS.$documentname;
        } else {
            $filename = $documentname.".pdf";
        }
		
        $pdf->Output($filename, $displaytype);
	}
	
	public static function emailReportPDF($userTestID)
	{
		$user = JFactory::getUser();
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
        $query->select('test_id, started_datetime, report_name');
		$query->from($db->quoteName('#__dnagifts_lnk_user_tests'));
		$query->where('id = '.$userTestID);
        $db->setQuery($query);
        $result = $db->loadObject();
		
		$filename = JPATH_SITE.DS."components".DS."com_dnagifts".DS."store".DS.$result->report_name;
		
        $subject = JText::_( 'COM_DNAGIFTS_REPORT_EMAILSUBJECT' ); 
        $body = JText::_( 'COM_DNAGIFTS_REPORT_EMAILMESSAGE' ); 
        $to = $user->get("email"); //louw.morne@gmail.com";
        $from = array('no-reply@dnagifts,co.za', JText::_( 'COM_DNAGIFTS_PDF_AUTHOR' ));
        
        # Invoke JMail Class
        $mailer = JFactory::getMailer();
         
        # Set sender array so that my name will show up neatly in your inbox
        $mailer->setSender($from);
         
        # Add a recipient -- this can be a single address (string) or an array of addresses
        $mailer->addRecipient($to);
         
        $mailer->setSubject($subject);
        $mailer->setBody($body);
         
        # If you would like to send as HTML, include this line; otherwise, leave it out
        $mailer->isHTML();
        
        //$attachment = JPATH_SITE."/README.txt";
        $mailer->addAttachment($filename);
        
        # Send once you have set all of your options
        $mailer->send();
	}
}
