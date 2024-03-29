var login_registration = {
	_init:function () {
	},
	login: function () {
		var logBtn = document.querySelector('.login-btn');
		if(logBtn) {
			logBtn.onclick = function () {
				var _info = document.querySelectorAll('input');
				var _login = {action:'login'};
				for( var t = 0; t < _info.length; t++) {
					_login[_info[t].name] = _info[t].value;
				}
				$.post("server/api/",_login, function(data) {
					if(data == 1) {
						window.location.href = "/dashboard";
					}
					else {
						var _message = document.getElementById('message');
						_message.innerHTML = "The combination presented does not match";
					}
				});
			}
		}
	},
	register: function() {
		var logBtn = document.querySelector('.login-btn');
		if(logBtn) {
			(function(elm) {
				logBtn.onclick = function () {
					var _info = document.querySelectorAll('input');
					var _message = document.getElementById('message');
					_message.innerHTML = "";
					var _login = {action:'register'};
					for( var t = 0; t < _info.length; t++) {
						if(_info[t].value.length > 0)
							_login[_info[t].name] = _info[t].value;
						else {
							_message.innerHTML = "Please fill out all fields";
							return;
						}
					}
					$.post("server/api/",_login, function(data) {
						_message.innerHTML = "<span style = 'color:#000000'>"+data+"</span>";
					});
				}
			})(this);
			
		}
	}
}

var calendar_builder = {
	months: ["January","February","March","April","May","June","July","August","September","October","November","December"],
	_year: null,
	_curr_month:null,
	_date: null,
	_modal: null,
	_pick_date: null,
	_init: function() {
		this._date = new Date();
		this._year = this._date.getFullYear();
		this._curr_month = this._date.getMonth();
		this._modal = document.getElementsByClassName('step-modal')[0];
		this.gatherInfo();
		var _next = document.getElementsByClassName('next')[0];
		var _prev = document.getElementsByClassName('prev')[0];
		(function(elm) {
			_next.onclick = function() {
				elm.nextMonth();
			}
			_prev.onclick = function() {
				elm.prevMonth();
			}
			window.onclick = function(event) {
				if(event.target == elm._modal) {
					elm._modal.style.display = "none";
				}
			}
			document.getElementsByClassName('close')[0].onclick = function() {
				elm._modal.style.display = "none";
			}
			document.getElementsByClassName('undo')[0].onclick = function() {
				elm._modal.style.display = "none";
			}
		})(this);

	},
	generateCalendar: function(stepCount) {
		var _first = new Date(this._year, this._curr_month,1).getDay();
		var _last = new Date(this._year, this._curr_month+1,0).getDate();
		var _prev = new Date(this._year, this._curr_month-1,0).getDate();
		var _days = "";
		var _start = 0;
		document.getElementById("month-year").innerHTML = this.months[this._curr_month] + " " + this._year;
		for(_start = 1; _start <= _last; ) {
			_days += "<tr>";
			if(_start == 1) {
				for(var t = _first; t >0; t--) {
					_days += "<td class = 'nc-month'>"+(_prev - t)+"</td>";
				}
			}
			else
				_first = 0;
			for (var _week = _first; _week < 7; _week ++) {
				var _stepsDone = stepCount[_start];
				var color = "";
				if(typeof(_stepsDone) == "undefined") {
					_stepsDone = "";
				}
				else {
					_stepsDone = "<div class = 'steps'>"+_stepsDone + "</div>";
					color = "style = 'background:#68687d91;'";
				}
				if(_start <= _last) {
					if(_start == this._date.getDate() && this._curr_month == this._date.getMonth() && this._year == this._date.getFullYear())
						_days += "<td class = 'c-month today' "+color+"><span>"+(_start )+"</span>"+_stepsDone+"</td>";
					else
						_days += "<td class = 'c-month' "+color+"><span>"+(_start )+"</span>"+_stepsDone+"</td>";
					_start++;
				}
				else {
					_days += "<td class = 'nc-month'>"+(_start-_last )+"</td>";
					_start++;
				}
			}
			_days += "</tr>";
		}
		document.querySelector("table tbody").innerHTML = _days;

		this.recordSteps();
	},
	prevMonth:function() {
		this._curr_month -= 1;
		if(this._curr_month < 0) {
			this._curr_month = 11;
			this._year -= 1;
		}
		this.gatherInfo();
	},
	nextMonth:function() {
		this._curr_month += 1;
		if(this._curr_month > 11) {
			this._curr_month = 0;
			this._year += 1;
		}
		this.gatherInfo();
	},
	gatherInfo:function() {
		(function(elm) {
			$.post("server/api/",{
				action:'getSteps',
				month:elm._curr_month+1
			}, function(data) {
				elm.generateCalendar(data);
			},"json");
		})(this);
	},
	recordSteps: function() {
		var table = document.querySelectorAll(".c-month");
		for(var t = 0; t< table.length; t++)
		{
			(function(elm) {
				table[t].onclick = function(event) {
					elm._modal.style.display = "block"
					document.getElementsByClassName('step-count')[0].value = "";
					var _day = this.querySelector('span').innerText.padStart(2,0);
					var _month = String(elm._curr_month + 1);
					_month = _month.padStart(2,0);
					elm._pick_date = elm._year+ "-"+_month + "-"+_day;
					var tag = _month + "/"+_day+ "/"+elm._year;
					document.getElementById('date').innerHTML = tag;
					elm.addSteps(this);
				}
			})(this);
		}
	},
	addSteps: function(_caldate) {
		(function(elm,_cd) {
			document.getElementsByClassName('record')[0].onclick = function() {
				var _steps = document.getElementsByClassName('step-count')[0].value;
				$.post("server/api/",{ 
					action:"addSteps",
					steps:_steps,
					timestamp:elm._pick_date
				},function(data) {
					elm._modal.style.display = "none";
					if(data.result) {
						_cd.style.background = "#68687d91";
						_cd.innerHTML += "<span class = 'steps'>"+_steps+"</span>";
						if(data.status > 0) {
							document.getElementsByClassName('conquering')[0].style.display = "block";
							window.setTimeout(function(){
								document.getElementsByClassName('conquering')[0].style.display = "none";
							},5000);
						}
					}
				},"json");
			}

		})(this,_caldate);
	}
}

var update_profile = {
	btn:null,
	_init:function() {
		this.btn = document.getElementsByClassName('update-info')[0];
		this.btn.onclick = function() {
			var _pass = document.querySelector('input[name="user-password"]').value;
			$.post("server/api/",{
				action:'update-profile',
				_pass:document.querySelector('input[name="user-password"]').value,
				_name:document.querySelector('input[name="first_name"]').value,
				_email:document.querySelector('input[name="user-email"]').value
			},function(data){
				if(data.result) {
					document.getElementById("message").innerHTML = "Your Profile has been successfully updated.";
				}
				else {
					document.getElementById("message").innerHTML = "We have encountered an error, talk to Jose.";
				}
			},"json");
		}
	}
}
