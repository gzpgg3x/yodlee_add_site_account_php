App.Models.Log = Backbone.Model.extend({
	defaults:{
		method:"POST",
		key: "0",
		short_url:"",
		long_url:"",
		request:{
			timer:"",
			body:""
		},
		request:{
			timer:"",
			body:""
		},
	}
});

App.Collections.Logs =Backbone.Collection.extend({
	model: App.Models.Log
});

App.Views.ViewPanelLog = Backbone.View.extend({
	tagName: 'li',
	template: _.template($("#template_panel_log").html()),
	events: {
		"click .btn-expand-detail" : "expand_details"
	},
	initialize: function(opt){},
	expand_details: function(evt){
		evt.preventDefault();		
		var view_modal = new App.Views.ViewModal({model: this.model});		
		$("#win_modal").find(".method_expand").text(this.model.get("method")+":");
		$("#win_modal").find(".short_url_expand").text(this.model.get("short_url"));	
		$("#win_modal").find("div.modal-body").html(view_modal.render().el);		
		$("#win_modal").modal("show");			
		try{
				
			$("#win_modal").find(".request-expanded").val(JSON.stringify(JSON.parse(this.model.get("request").body),null,2));	
		}catch(e){
			$("#win_modal").find(".request-expanded").val(this.model.get("request").body);
				
		}
		try{	
			$("#win_modal").find(".response-expanded").val(JSON.stringify(JSON.parse(this.model.get("response").body),null,2));	
				
		}catch(e){				
			$("#win_modal").find(".response-expanded").val(this.model.get("response").body);
		}	
		var request_expanded=$("#win_modal").find(".request-expanded");
		var response_expanded=$("#win_modal").find(".response-expanded");		
		setTimeout(function(){			
			request_expanded.codemirror({
				theme: "eclipse",
				lineWrapping:true,
				lineNumbers: true,
				mode: "application/json"
			});	
			response_expanded.codemirror({
				theme: "eclipse",
				lineWrapping:true,
				lineNumbers: true,
				mode: "application/json"
			});
			request_expanded.setSize(400, 300);
			response_expanded.setSize(400, 300);
		},200); 
	},
	render: function(){
		id_accordion = new Date().getTime();
		this.$el.html(this.template(this.model.toJSON()));
		this.$el.find(".accordion-toggle").attr("href","#"+id_accordion);
		this.$el.find(".accordion-body").attr("id", id_accordion);			
		this.$el.find('.request').html(JSON.stringify(JSON.parse(this.model.get("request").body),null,2));
		this.$el.find('.response').html(JSON.stringify(JSON.parse(this.model.get("response").body),null,2));
		return this;
	}
});

App.Views.ViewModal = Backbone.View.extend({
	tagName: 'div',
	template: _.template($("#template_modal").html()),
	events: {},
	render: function(){
		this.$el.html(this.template(this.model.toJSON()));       		
		return this;
	}
});

App.Instances.Collection.Logs =  new App.Collections.Logs();

App.Instances.Collection.Logs.on("add", function(currentModel){
	var view = new App.Views.ViewPanelLog({model: currentModel});
	$("div.logger").find("#accordion_log").append(view.render().el);
});

					