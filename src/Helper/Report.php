<?php

namespace SellingPartner\Helper;


class Report
{
 
	/**
	 * @param $payload : Response from getReportDocument Function. e.g.: response['payload']
	 * @return string : Processing Report.
	 */
	public function downloadProcessingReport($payload)
	{
		return Document::download($payload);
	}
}
