
$(".vi_container").keydown(function(e){
	if (e.keyCode == 27) {
		vi_save();
	}
}).click( function() {
	$(this).focus();
}).autosize();

function vi_initialize() {
	$('.vi_container').focus()[0]
		.setSelectionRange($('.vi_container').val().length,$('.vi_container').val().length);
	
	$(document).scrollTop($(document).height());
	
	$("html, body").click( function() {
		$(".vi_container").focus();
	});
}

function vi_save() {
	$.post( "bin/bash/vi/vi_save.php", { "input" : $(".vi_container").val() }, function(data) {
		htmlStr = $('<div/>').text($(".vi_container").val()).html();
		$(".vi_container").replaceWith(function () {
			return "<span>" + vi_nl2br(htmlStr) + "<br></span>";
		});
		$(".vi_container").removeClass("vi_container").removeClass("writable");
		workIsDone();
	});
}

function vi_nl2br (str) {  
	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
}
