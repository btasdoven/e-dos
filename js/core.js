Array.prototype.remove = function(obj) {
	var i = this.indexOf(obj);
	this.splice(i, 1);
	return this;
};

function run(input, callback) {
	$.post("run.php", { "input" : input, token : sessiontoken }, function(data) {
		//alert(data);
		
		data = $.parseJSON(data.substr( data.indexOf("{") ));
		
		if (typeof callback != 'undefined')
			callback(data);
	});
}

function bringWindowFront(self) {
		Window.activeWindow.removeClass("active");
		self.window.addClass("active");
		
		var ii = self.window.css('z-index');
		var ai = Window.activeWindow.css('z-index');
		$(".window").css('z-index', function (index) {
			var zi = $(this).zIndex();
			if (zi > ii) {
				return zi - 1;
			}
			return zi;
		});
		
		self.window.css('z-index', ai);
		Window.activeWindow.find(".window_cover").show().focus();
		self.window.find(".window_cover").hide();
		$(self.window.iframe).focus();
		Window.activeWindow = self.window;
		if (self.children.length)
			bringWindowFront(self.children[self.children.length-1]);

}

function Window(url, title, icon, isExecutable, caller, params, left, top, width, height) {

	this.url = url;
	this.title = title;
	this.icon = icon;
	this.isExecutable = isExecutable;
	
	this.caller = caller;
	this.params = params;
	this.iframe;
	
	this.children = new Array();
	
	this.proc = Window.procId++;
	Window.procList.push(this);
	
	this.open(left, top, width, height); 
}

Window.baseWind = $('.window:first');
Window.procId = 1;
Window.procList = new Array();
Window.activeWindow = Window.baseWind;

Window.prototype.open = function(left, top, width, height) {
	
	var self = this;
	
	//window = $("<div class='window'><div class = 'window_header'><img src = '' height = '20px' style = 'vertical-align: middle; margin-top: -1px;'><span class = 'window_name'></span><div class = 'window_close_btn'>×</div></div><div class = 'window_iframe_cont'><iframe id = 'iframe' class = 'window_iframe' width = '100%' height = '100%' frameborder = '0'></iframe></div></div>");
	this.window = $(".window:first").clone();
		
	if (typeof left != 'undefined')
		self.window.css('left', left + 'px');
	else
		self.window.css('left', (Window.procList.length * 20 + 100) + 'px');
		
	if (typeof top != 'undefined')
		self.window.css('top', top + 'px');
	else
		self.window.css('top', (Window.procList.length * 20 + 100) + 'px');
		
	if (typeof width != 'undefined')
		self.window.css('width', width + 'px');
		
	if (typeof height != 'undefined')
		self.window.css('height', height + 'px');
		
	$("#container").append(self.window);	
	self.window.css('z-index', parseInt(Window.activeWindow.css('z-index')) + 1);

	

	var ifrm = self.window.find('.window_iframe')[0];
	ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument;
	self.window.iframe = ifrm;	
	self.window.iframe.thou = self;
	
	if (typeof self.isExecutable != 'undefined' && self.isExecutable) {
		var inp = (typeof self.params == 'undefined') ? "" : self.params;
		$.post(self.url, { input : inp }, function(data) {
			self.window.iframe.document.open();
			self.window.iframe.document.write(data);
			self.window.iframe.document.close();
		});
	}
	else
		self.window.find('.window_iframe').attr('src', self.url);
	
	if (typeof self.caller != 'undefined') {
		self.window.iframe.caller = self.caller;
		self.caller.thou.children.push(self);
	}
				
	self.window.find('.window_iframe_cont').css('height', 'calc( 100% - '+self.window.find(".window_header").height()+'px)');
	self.window.find('span').html(self.title);
	
	if (self.isExecutable)
		self.window.find('img:first').prop('src', self.icon);
	else
		self.window.find('img:first').attr('data-filename', self.title);
	
	self.window.find(".window_cover").click( function(e) { //self.window.mousedown( function() {
		bringWindowFront(self);
		$(self.window.iframe).focus();
		e.preventDefault();
	});
	
	self.window.find(".window_header").click( function(e) {
		bringWindowFront(self);
		e.preventDefault();
	});
	
	self.window.find(".window_header").dblclick( function(e) {
		self.window.toggleClass('window_maximized');
		self.window.trigger('resize');
		$(self.window.iframe).focus();
		e.preventDefault();
		return false;
	});
	
	self.window.find(".window_close_btn#maximize_btn").click( function(e) {
		self.window.toggleClass('window_maximized');
		self.window.trigger('resize');
		$(self.window.iframe).focus();
		e.preventDefault();
	});
	
	self.window.find(".window_close_btn#close_btn").click( function(e) {
		
		Window.procList = Window.procList.remove(self);
		
		if (typeof self.caller != 'undefined')
			self.caller.thou.children = self.caller.thou.children.remove(self);		
		
		max = Window.procList[0];
		for (var i = 1; i < Window.procList.length; ++i)
			if (Window.procList[i].window.zIndex() > max.window.zIndex())
				max = Window.procList[i];
		
		if (typeof max != 'undefined')
			bringWindowFront(max);
		
		// close children windows
		for( var i = 0; i < self.children.length; ++i)
			self.children[i].window.find(".window_close_btn#close_btn").trigger('click');
		
		$(this).parent().parent().remove(); 
		
		if (typeof self.caller != 'undefined' && typeof self.caller.workIsDone != 'undefined')
			self.caller.workIsDone();
			
		$(Window.activeWindow.iframe).focus();
		e.preventDefault();
	});

	
	
	self.window.resizable({
		containment: 'parent',
		minWidth: 400,
		minHeight: 300,
		start: function () {
			bringWindowFront(self);
			self.window.find(".window_cover").show();
		},	
		stop: function () {
			self.window.find(".window_cover").hide();
		},
		handles: 'n, e, s, w, ne, se, sw, nw'
	}).draggable({ 
		containment: 'parent',
		handle: ".window_header",
		cancel: ".window_close_btn",
		cursor: "move",
		start: function () {
			bringWindowFront(self);
			self.window.find(".window_cover").show();
		},	
		stop: function () {
			self.window.find(".window_cover").hide();
		}
	}).css('position', 'absolute').find('.ui-icon-gripsmall-diagonal-se').css('background-position', '16px 16px');
	
	self.window.resize(function() {
		$(this).find('.window_iframe_cont').css('height', 'calc( 100% - '+self.window.find(".window_header").height()+'px)');
	});

	self.window.css('display', 'block');
	bringWindowFront(self);
	self.window.css('z-index', parseInt(Window.activeWindow.css('z-index')) + 1);			//Ustte de index atamasi yapilmali.
}
	

	
