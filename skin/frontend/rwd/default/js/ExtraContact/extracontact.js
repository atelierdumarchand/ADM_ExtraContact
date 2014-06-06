;(function($) {
	/**
	 * Create form
	 * @param string formId Form ID
	 * @param string firstFieldFocus
	 */
	ExtraContactForm = function(formId, firstFieldFocus)
	{
		this.formId = formId;
		// create VarienForm instance
		new VarienForm(this.formId, firstFieldFocus);
	};
	
	/**
	 * Initialize subject selector
	 * @param string subjectSelector Subject selector ID (select element)
	 * @param string messageSelector Message selector ID (textarea element)
	 * @param Array datas List of messages
	 */
	ExtraContactForm.prototype.initSubjectSelector = function(subjectSelector, messageSelector, datas)
	{
		$('#' + subjectSelector).on('change', function() {
			$('#' + messageSelector).html(datas[$(this).val()]);
		}).change();
	};
}(jQuery));
