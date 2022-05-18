<?php

namespace SellingPartner\Helper;


class Report
{
 
	/**
	 * @param $payload : Response from getReportDocument Function. e.g.: response['payload']
	 * @param $to_encoding
	 * @return string : Processing Report.
	 */
	public function downloadProcessingReport($payload,$to_encoding='utf-8')
	{
		return Document::download($payload,$to_encoding);
	}
}
