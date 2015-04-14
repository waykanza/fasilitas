function qalert(q) { jQuery('html').prepend(q); }

var path				= window.location.href.split('/'),
	base_url			= path[0] + '//' + path[2] + '/' + path[3] + '/',
	
	base_vb				= base_url + 'vb/',
	base_adm			= base_url + 'administrator/',
	
	base_master			= base_adm + 'master/',
	base_master_fa		= base_adm + 'master/fasilitas/',
	
	base_laporan		= base_adm + 'laporan/',	
	base_pembayaran		= base_adm + 'pembayaran/',
	base_pembayaran_fasilitas = base_pembayaran + 'fasilitas/',
	base_bank			= base_adm + 'bank/',
	base_periode		= base_adm + 'periode/',
	base_invoice		= base_adm + 'invoice/',
	base_posting		= base_adm + 'posting/',
	base_faktur_pajak	= base_adm + 'faktur_pajak/',
	
	
	winWidth = jQuery(window).width(),
	winHeight = jQuery(window).height(),
	popup

function ajax_start() {
	jQuery('<div id="wait"><span>Mohon tunggu...</span></div>').prependTo('body');
}
function ajax_stop() {
	jQuery('#wait').remove();
}

jQuery(document).ajaxStart(function(){
	ajax_start();
});
jQuery(document).ajaxStop(function(){
	ajax_stop();
});

jQuery(function($) {

	$(document).on('click', '#cb_all', function() {
		$('.cb_data').prop('checked', this.checked);
	});
	
	/* dd-mm-yyyy */
	$('.dd-mm-yyyy').Zebra_DatePicker({
		format: 'd-m-Y',
		readonly_element : false,
		inside: true
	});
	
	/* mm-yyyy */
	$('.mm-yyyy').Zebra_DatePicker({
		format: 'm-Y',
		readonly_element : false,
		inside: true
	});
	
	/* yyyy */
	$('.yyyy').Zebra_DatePicker({
		format: 'Y',
		readonly_element : false,
		inside: true
	});
	
	$(document).on('keyup', '.page_num', function(e) {
		e.preventDefault();
		$('.page_num').val($(this).val());
	});
	
	$('.dd-mm-yyyy').inputmask({  mask: 'd-m-y', placeholder: 'dd/mm/yyyy', clearIncomplete: true });
	$('.mm-yyyy').inputmask({ mask: 'm-y', placeholder: 'mm/yyyy', clearIncomplete: true });
	$('.yyyy').inputmask({ mask: 'y', placeholder: 'yyyy', clearIncomplete: true });
	$('#per_page').inputmask('integer', { repeat: '3' });
});

function to_decimal(s)
{
	s = s.replace(/[^0-9.]/g, '');
	s = (s == '') ? '0' : s
	return parseFloat(s);
}

function set_ddmmyyyy(obj)
{
	obj
		.val('')
		.attr('size', '13')
		.Zebra_DatePicker({format: 'd-m-Y', readonly_element : false, inside: true})
		.inputmask({  mask: 'd-m-y', placeholder: 'dd/mm/yyyy', clearIncomplete: true });
}
function set_mmyyyy(obj)
{
	obj
		.val('')
		.attr('size', '10')
		.Zebra_DatePicker({format: 'm-Y', readonly_element : false, inside: true})
		.inputmask({ mask: 'm-y', placeholder: 'mm/yyyy', clearIncomplete: true });
}
function detroy_format(obj, sz)
{
	if(typeof(sz) === 'undefined') { sz = '20'; }
	var dp = obj.data('Zebra_DatePicker');
	dp.destroy();
	obj.attr('size', sz).inputmask('remove');
}

function t_strip(obj)
{
	jQuery(obj + ' tbody tr').each(function(index) {
		if (index % 2 != 0){ jQuery(this).addClass('strip'); }
	});
}

function t_scroll(obj)
{
	jQuery(obj).addClass('t-scroll').height(jQuery(window).height()-100);
}

/* POPUP */
function setPopup(title, url, width, height)
{
	if (popup) { popup.close(); }
	
	popup = new Window('popup', {
		url				: url,
		title			: title,
		className		: 'mac_os_x',
		width			: width,
		height			: height,
		destroyOnClose	: true,
		zIndex			: 150,
		recenterAuto	: false
		//showEffect	: Effect.BlindDown
	});
	
	popup.showCenter();
	popup.toFront();
}

function alertPopup(ttl, msg, height, width)
{
	if(typeof(width) === 'undefined') width = 450;
	if(typeof(height) === 'undefined') height = 100;
	
	Dialog.alert('<div style="word-break: break-word;padding:10px 15px;">' + msg + '</div>', {
		top:0,
		windowParameters: {
			className: 'mac_os_x', 
			width:width,
			height:height,
			zIndex:250,
			title: ttl,
			draggable:true,
			resizable:true
		}, 
		okLabel: ' Ok ',
		onOk:function(win) { return true; }
	});
}

/* POPUP PRINT */
function open_print(url, prt) {
	
	if(typeof(prt) === 'undefined') { prt = ''; }
	
	var win,
		trg = '_blank',
		sheight = screen.height,
		swidth = screen.width,
		trg = '_blank',
		set = [
			'height=' + (sheight - 100),
			'width=' + (swidth - 100),
			//'top=' + ((sheight/2) - ((sheight - 100)/2)),
			'top=0',
			'left=' + ((swidth/2) - ((swidth - 100)/2)),
			'fullscreen=yes',
			'location=no',
			'titlebar=no',
			'menubar=no',
			'scrollbars=1',
			'resizable=1'
		].join(',');

	if (prt == '') {
		win = window.open(url, trg, set);
	} else if (prt == '1') {
		win = parent.window.open(url, trg, set);
	} else if (prt == '2') {
		win = parent.parent.window.open(url, trg, set);
	}
	
}