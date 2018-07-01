var vu=new Vue({
  el:'#app',
  data:{
		url: URL,
		me: JSON.parse($('#account').text()),
		nowSel: 0
  },
	methods:{
		setSel:function(index){
			this.nowSel=index;
		}
	},
	created:function(){
		this.nowSel=4;
		
	}
});