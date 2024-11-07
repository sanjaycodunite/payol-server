/*!
 * The Final Countdown for jQuery v2.2.0 (http://hilios.github.io/jQuery.countdown/)
 * Copyright (c) 2016 Edson Hilios
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
(function(factory) {
    "use strict";
    if (typeof define === "function" && define.amd) {
        define([ "jquery" ], factory);
    } else {
        factory(jQuery);
    }
})(function($) {
    "use strict";
    var instances = [], matchers = [], defaultOptions = {
        precision: 100,
        elapse: false,
        defer: false
    };
    matchers.push(/^[0-9]*$/.source);
    matchers.push(/([0-9]{1,2}\/){2}[0-9]{4}( [0-9]{1,2}(:[0-9]{2}){2})?/.source);
    matchers.push(/[0-9]{4}([\/\-][0-9]{1,2}){2}( [0-9]{1,2}(:[0-9]{2}){2})?/.source);
    matchers = new RegExp(matchers.join("|"));
    function parseDateString(dateString) {
        if (dateString instanceof Date) {
            return dateString;
        }
        if (String(dateString).match(matchers)) {
            if (String(dateString).match(/^[0-9]*$/)) {
                dateString = Number(dateString);
            }
            if (String(dateString).match(/\-/)) {
                dateString = String(dateString).replace(/\-/g, "/");
            }
            return new Date(dateString);
        } else {
            throw new Error("Couldn't cast `" + dateString + "` to a date object.");
        }
    }
    var DIRECTIVE_KEY_MAP = {
        Y: "years",
        m: "months",
        n: "daysToMonth",
        d: "daysToWeek",
        w: "weeks",
        W: "weeksToMonth",
        H: "hours",
        M: "minutes",
        S: "seconds",
        D: "totalDays",
        I: "totalHours",
        N: "totalMinutes",
        T: "totalSeconds"
    };
    function escapedRegExp(str) {
        var sanitize = str.toString().replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
        return new RegExp(sanitize);
    }
    function strftime(offsetObject) {
        return function(format) {
            var directives = format.match(/%(-|!)?[A-Z]{1}(:[^;]+;)?/gi);
            if (directives) {
                for (var i = 0, len = directives.length; i < len; ++i) {
                    var directive = directives[i].match(/%(-|!)?([a-zA-Z]{1})(:[^;]+;)?/), regexp = escapedRegExp(directive[0]), modifier = directive[1] || "", plural = directive[3] || "", value = null;
                    directive = directive[2];
                    if (DIRECTIVE_KEY_MAP.hasOwnProperty(directive)) {
                        value = DIRECTIVE_KEY_MAP[directive];
                        value = Number(offsetObject[value]);
                    }
                    if (value !== null) {
                        if (modifier === "!") {
                            value = pluralize(plural, value);
                        }
                        if (modifier === "") {
                            if (value < 10) {
                                value = "0" + value.toString();
                            }
                        }
                        format = format.replace(regexp, value.toString());
                    }
                }
            }
            format = format.replace(/%%/, "%");
            return format;
        };
    }
    function pluralize(format, count) {
        var plural = "s", singular = "";
        if (format) {
            format = format.replace(/(:|;|\s)/gi, "").split(/\,/);
            if (format.length === 1) {
                plural = format[0];
            } else {
                singular = format[0];
                plural = format[1];
            }
        }
        if (Math.abs(count) > 1) {
            return plural;
        } else {
            return singular;
        }
    }
    function getServerTime(dataval)
    {

        $.ajax({                
            url:'https://www.payol.in/livetime.php',
            success:function(r){
                    var timedata = JSON.parse($.trim(r));
                    var now = timedata['currentDateTime'];
                    var nowGetTime = timedata['currentTime'];
                    var nowYear = timedata['currentYear'];


                    /*var now = new Date();
                    console.log(now);
                    console.log(now.getTime());
                    console.log(now.getFullYear());*/
                    var newTotalSecsLeft;

                    //newTotalSecsLeft = this.finalDate.getTime() - now.getTime();
                    newTotalSecsLeft = dataval.finalDate.getTime() - nowGetTime;
                    newTotalSecsLeft = Math.ceil(newTotalSecsLeft / 1e3);
                    newTotalSecsLeft = !dataval.options.elapse && newTotalSecsLeft < 0 ? 0 : Math.abs(newTotalSecsLeft);
                    if (dataval.totalSecsLeft === newTotalSecsLeft || dataval.firstTick) {
                        dataval.firstTick = false;
                        return;
                    } else {
                        dataval.totalSecsLeft = newTotalSecsLeft;
                    }

                    dataval.elapsed = now >= dataval.finalDate;
                    dataval.offset = {
                        seconds: dataval.totalSecsLeft % 60,
                        minutes: Math.floor(dataval.totalSecsLeft / 60) % 60,
                        hours: Math.floor(dataval.totalSecsLeft / 60 / 60) % 24,
                        days: Math.floor(dataval.totalSecsLeft / 60 / 60 / 24) % 7,
                        daysToWeek: Math.floor(dataval.totalSecsLeft / 60 / 60 / 24) % 7,
                        daysToMonth: Math.floor(dataval.totalSecsLeft / 60 / 60 / 24 % 30.4368),
                        weeks: Math.floor(dataval.totalSecsLeft / 60 / 60 / 24 / 7),
                        weeksToMonth: Math.floor(dataval.totalSecsLeft / 60 / 60 / 24 / 7) % 4,
                        months: Math.floor(dataval.totalSecsLeft / 60 / 60 / 24 / 30.4368),
                        //years: Math.abs(this.finalDate.getFullYear() - now.getFullYear()),
                        years: Math.abs(dataval.finalDate.getFullYear() - nowYear),
                        totalDays: Math.floor(dataval.totalSecsLeft / 60 / 60 / 24),
                        totalHours: Math.floor(dataval.totalSecsLeft / 60 / 60),
                        totalMinutes: Math.floor(dataval.totalSecsLeft / 60),
                        totalSeconds: dataval.totalSecsLeft
                    };
                    console.log(dataval.offset);
                    if (!dataval.options.elapse && dataval.totalSecsLeft === 0) {
                        dataval.stop();
                        dataval.dispatchEvent("finish");
                    } else {
                        dataval.dispatchEvent("update");
                    }

                    return dataval;
                    
                }
            });
    }
    var Countdown = function(el, finalDate, options) {
        this.el = el;
        this.$el = $(el);
        this.interval = null;
        this.offset = {};
        this.options = $.extend({}, defaultOptions);
        this.firstTick = true;
        this.instanceNumber = instances.length;
        instances.push(this);
        this.$el.data("countdown-instance", this.instanceNumber);
        if (options) {
            if (typeof options === "function") {
                this.$el.on("update.countdown", options);
                this.$el.on("stoped.countdown", options);
                this.$el.on("finish.countdown", options);
            } else {
                this.options = $.extend({}, defaultOptions, options);
            }
        }
        this.setFinalDate(finalDate);
        if (this.options.defer === false) {
            this.start();
        }
    };
    $.extend(Countdown.prototype, {
        start: function() {
            if (this.interval !== null) {
                clearInterval(this.interval);
            }
            var self = this;
            this.update();
            this.interval = setInterval(function() {
                self.update.call(self);
            }, this.options.precision);
        },
        stop: function() {
            clearInterval(this.interval);
            this.interval = null;
            this.dispatchEvent("stoped");
        },
        toggle: function() {
            if (this.interval) {
                this.stop();
            } else {
                this.start();
            }
        },
        pause: function() {
            this.stop();
        },
        resume: function() {
            this.start();
        },
        remove: function() {
            this.stop.call(this);
            instances[this.instanceNumber] = null;
            delete this.$el.data().countdownInstance;
        },
        setFinalDate: function(value) {
            this.finalDate = parseDateString(value);
        },
        update: async function() {
            if (this.$el.closest("html").length === 0) {
                this.remove();
                return;
            }
            
            //this.getServerTime();

            
            
            /*var now = this.nowTime;
            var nowGetTime = this.nowGetTime;
            var nowYear = this.nowYear;*/

            /*var now = 'Wed Aug 23 2023 11:52:07 GMT+0530 (India Standard Time)';
            var nowGetTime = this.nowGetTime;
            var nowYear = '2023';*/

            var now = new Date();
            var newTotalSecsLeft;

            newTotalSecsLeft = this.finalDate.getTime() - now.getTime();
            //newTotalSecsLeft = this.finalDate.getTime() - nowGetTime;
            newTotalSecsLeft = Math.ceil(newTotalSecsLeft / 1e3);
            newTotalSecsLeft = !this.options.elapse && newTotalSecsLeft < 0 ? 0 : Math.abs(newTotalSecsLeft);
            if (this.totalSecsLeft === newTotalSecsLeft || this.firstTick) {
                this.firstTick = false;
                return;
            } else {
                this.totalSecsLeft = newTotalSecsLeft;
            }

            this.elapsed = now >= this.finalDate;
            this.offset = {
                seconds: this.totalSecsLeft % 60,
                minutes: Math.floor(this.totalSecsLeft / 60) % 60,
                hours: Math.floor(this.totalSecsLeft / 60 / 60) % 24,
                days: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7,
                daysToWeek: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7,
                daysToMonth: Math.floor(this.totalSecsLeft / 60 / 60 / 24 % 30.4368),
                weeks: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 7),
                weeksToMonth: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 7) % 4,
                months: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 30.4368),
                years: Math.abs(this.finalDate.getFullYear() - now.getFullYear()),
                //years: Math.abs(this.finalDate.getFullYear() - nowYear),
                totalDays: Math.floor(this.totalSecsLeft / 60 / 60 / 24),
                totalHours: Math.floor(this.totalSecsLeft / 60 / 60),
                totalMinutes: Math.floor(this.totalSecsLeft / 60),
                totalSeconds: this.totalSecsLeft
            };
            
            if (!this.options.elapse && this.totalSecsLeft === 0) {
                this.stop();
                this.dispatchEvent("finish");
            } else {
                this.dispatchEvent("update");
            }
            
        },
        getServerTime: function(){
            

            var timedata = [];
            //var apiUrl = 'http://localhost/payol/livetime.php';
            var apiUrl = 'https://www.payol.in/livetime.php';
            var myThis = this;
            getSTime(myThis);
            function getSTime(myThis){
                $.ajax({                
                url:apiUrl,
                success:function(r){
                        var timedata = JSON.parse($.trim(r));
                        this.nowGetTime = timedata['currentTime'];
                        this.nowYear = timedata['currentYear'];
                        this.nowTime = parseDateString(timedata['currentDateTime']);

                        var newTotalSecsLeft;

                        //newTotalSecsLeft = this.finalDate.getTime() - now.getTime();
                        newTotalSecsLeft = myThis.finalDate.getTime() - this.nowGetTime;
                        
                        newTotalSecsLeft = Math.ceil(newTotalSecsLeft / 1e3);
                        
                        newTotalSecsLeft = !myThis.options.elapse && newTotalSecsLeft < 0 ? 0 : Math.abs(newTotalSecsLeft);
                        

                        if (myThis.totalSecsLeft === newTotalSecsLeft || myThis.firstTick) {
                            myThis.firstTick = false;
                            return;
                        } else {
                            myThis.totalSecsLeft = newTotalSecsLeft;
                        }

                        myThis.elapsed = this.nowTime >= myThis.finalDate;
                        myThis.offset = {
                            seconds: myThis.totalSecsLeft % 60,
                            minutes: Math.floor(myThis.totalSecsLeft / 60) % 60,
                            hours: Math.floor(myThis.totalSecsLeft / 60 / 60) % 24,
                            days: Math.floor(myThis.totalSecsLeft / 60 / 60 / 24) % 7,
                            daysToWeek: Math.floor(myThis.totalSecsLeft / 60 / 60 / 24) % 7,
                            daysToMonth: Math.floor(myThis.totalSecsLeft / 60 / 60 / 24 % 30.4368),
                            weeks: Math.floor(myThis.totalSecsLeft / 60 / 60 / 24 / 7),
                            weeksToMonth: Math.floor(myThis.totalSecsLeft / 60 / 60 / 24 / 7) % 4,
                            months: Math.floor(myThis.totalSecsLeft / 60 / 60 / 24 / 30.4368),
                            //years: Math.abs(this.finalDate.getFullYear() - now.getFullYear()),
                            years: Math.abs(myThis.finalDate.getFullYear() - this.nowYear),
                            totalDays: Math.floor(myThis.totalSecsLeft / 60 / 60 / 24),
                            totalHours: Math.floor(myThis.totalSecsLeft / 60 / 60),
                            totalMinutes: Math.floor(myThis.totalSecsLeft / 60),
                            totalSeconds: myThis.totalSecsLeft
                        };
                        
                        
                        if (!myThis.options.elapse && myThis.totalSecsLeft === 0) {
                            myThis.stop();
                            myThis.dispatchEvent("finish");
                        } else {
                            myThis.dispatchEvent("update");
                        }

                        console.log(myThis.totalSecsLeft);


                    }
                });
            }
            
        },
        dispatchEvent: function(eventName) {
            var event = $.Event(eventName + ".countdown");
            event.finalDate = this.finalDate;
            event.elapsed = this.elapsed;
            event.offset = $.extend({}, this.offset);
            event.strftime = strftime(this.offset);
            this.$el.trigger(event);
        }
    });
    $.fn.countdown = function() {
        var argumentsArray = Array.prototype.slice.call(arguments, 0);
        return this.each(function() {
            var instanceNumber = $(this).data("countdown-instance");
            if (instanceNumber !== undefined) {
                var instance = instances[instanceNumber], method = argumentsArray[0];
                if (Countdown.prototype.hasOwnProperty(method)) {
                    instance[method].apply(instance, argumentsArray.slice(1));
                } else if (String(method).match(/^[$A-Z_][0-9A-Z_$]*$/i) === null) {
                    instance.setFinalDate.call(instance, method);
                    instance.start();
                } else {
                    $.error("Method %s does not exist on jQuery.countdown".replace(/\%s/gi, method));
                }
            } else {
                new Countdown(this, argumentsArray[0], argumentsArray[1]);
            }
        });
    };
});