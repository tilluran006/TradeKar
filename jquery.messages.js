$(document).ready(function () {

	var newmsgtimer;

	(function updConv() {
		$.ajax({
			url: 'conversations.php',
			success: function(result,status,xhr) {
				var xml = $(result);
				var right = $('#right');
				var active = $('#right .conversation.active').attr('id');
				right.empty();
				var n= xml.find('CONVERSATION').each(function() {
					var name = $(this).children('NAME').text();
					var msg = $(this).children('TEXT').text();
					var conid = $(this).attr('id');
					var conv = $('<div></div>');
					conv.attr('id', conid);
					if(conid == active) {
						conv.addClass('active');
					}
					conv.click(function() {
						openMsgBox();
						$(this).siblings().removeClass('active');
						$(this).addClass('active');
						updMsg();
					});
					conv.addClass('conversation');
					var t = "<div class='name'>" + name + "</div><div class='msg'>" + msg + "</div>";
					conv.append(t);
					right.append(conv);
				});
			},
			complete: function() {
				setTimeout(updConv, 1000);
			}
		});
	})();

	$(".message-header >.cross").click(function() {
		$(this).parents(".message-box").hide();
		clearTimeout(newmsgtimer);
	});

	function openMsgBox() {
		$(".message-box").show();
		var msgbody = $('.message-box .message-body');
  		msgbody.siblings('.message-input').children('textarea').focus();
	};

	function updMsg() {
		var id = $('.conversation.active').attr('id');
		var link = 'messages.php?convid=' + id;
		var msgbody = $('.message-box .message-body');
		$.ajax({
			url: link,
			success: function(result,status,xhr) {
				var xml = $(result);
				msgbody.empty();
				xml.find('MESSAGE').each(function() {
					var main = $('<div></div>');
					main.attr('id', $(this).attr('id'));
					main.addClass('message');
					var t = $(this).children('ME').text();
					var sent = $(this).children('TIME').text();
					if( t == "") {
						main.addClass('left');
						var t = $(this).children('OTHER').text();
					}else {
						main.addClass('right');
					}
					var text = "<p>"+ t + "</p><div class='quote'></div><label>"+ sent + "</label>";
					main.append(text);
					msgbody.append(main);
				});
				goToBottom();
  				updNewMsg();
   			}
		});
	}

	function updNewMsg() {
		var id = $('.conversation.active').attr('id');
		var link = 'messages.php?convid=' + id;
		var msgbody = $('.message-box .message-body');
		var last = msgbody.children().last().children('label').text();
		$.ajax({
			url: link,
			success: function(result,status,xhr) {
				var xml = $(result);
				xml.find('MESSAGE').each(function() {
					var d = $(this).children('TIME').text();
					if(Date.parse(d) > Date.parse(last)) {
						var main = $('<div></div>');
						main.attr('id', $(this).attr('id'));
						main.addClass('message');
						var t = $(this).children('ME').text();
						if( t == "") {
							main.addClass('left');
							var t = $(this).children('OTHER').text();						
							var text = "<p>"+ t + "</p><div class='quote'></div><label>"+ d + "</label>";
							main.append(text);
							msgbody.append(main);
							goToBottom();
						}
					}					
				});
			},
			complete: function() {
				newmsgtimer = setTimeout(updNewMsg, 1000);
			}
		});
	}

	function goToBottom() {
		var msgbody = $('.message-body');
		var height = msgbody[0].scrollHeight;
  		msgbody.scrollTop(height);
	}

	$('.message-input button').click(function() {
		var t = $(this).siblings().val();		
		$(this).siblings().val("").focus();
		var main = $('<div></div>');
		main.addClass('message right');
		var text = "<p>"+ t + "</p><div class='quote'></div><label>"+ "Sending..." + "</label>";
		main.append(text);
		$('.message-body').append(main);
		$.post("sendmessage.php",
		{
			convid: $('.conversation.active').attr('id'),
			msg: t
		},function (data, status) {
			var temp = data.split(',');
			var id = temp[0];
			var date = temp[1];
			main.attr('id', id);
			main.children('label').text(date);
		});
		goToBottom();
	});

});