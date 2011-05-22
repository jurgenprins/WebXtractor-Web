function wxOfferStore(url) {
	return new Ext.data.JsonStore({
		restful: false,
		proxy : new Ext.data.HttpProxy({
	    api: {
	    		read: 		{url: url, method: 'GET'},
			    load: 		{url: url, method: 'GET'},
			    create:		{url: url, method: 'POST'},
			    update: 	{url: url, method: 'PUT'},
			    save: 		{url: url, method: 'PUT'},
			    destroy: 	{url: url, method: 'DELETE'}
			},
			headers: {'Accept': 'application/json'}
	 	}),
		root: 'offers',
		fields: ['id', 'name', 'url', 'img', 'itemname', 'datasource', {name: 'created', type: 'date', dateFormat:'timestamp'}, {name: 'updated', type: 'date', dateFormat:'timestamp'}, 'confidence', 'shown', 'rank'],
		idProperty: 'id',
		totalProperty: 'total',
		successProperty: 'success',							
		autoLoad: true,
		autoSave: false,
		remoteSort: true,
		sortInfo: {
			field: 'updated',
			direction: 'desc',
		},
		writer: new Ext.data.JsonWriter({ encode: false, writeAllFields: false }),
		baseParams: {start:0,limit:10,new:1},
		listeners: {
			beforeload: function(store, options) {
				if (Ext.getCmp('offer-data-query')) {
					options.params.query = Ext.getCmp('offer-data-query').getValue();
				}
				if (Ext.getCmp('offer-data-new-cb')) {
					options.params.new 	= Ext.getCmp('offer-data-new-cb').getValue() ? 1 : 0;
				}
				if (Ext.getCmp('offer-data-slider')) {
					options.params.rank = Ext.getCmp('offer-data-slider').getValue();
				}
				if (Ext.getCmp('offer-data-rank-cb')) {
					options.params.rankup = Ext.getCmp('offer-data-rank-cb').getValue() ? 1 : 0;
				}
			}
    }
	});
}
function wxOfferView(offerStore, bTitle) {
	return {
		title: bTitle ? 'Offers' : '',
		id: 'offer-view',
		frame: true,
		layout: 'fit',
		border: true,
		autoScroll: true,
		items: [{
			xtype: 'dataview',
			id: 'offer-data-view',
			layout:	'fit',
			border:	false,
			loadMask: true,
			store: offerStore,
			columns: [
				{ id: 'name', dataIndex: 'name', header: 'Name', width: '30%', sortable: true },
				{ dataIndex: 'url', header: 'URL'},
				{ dataIndex: 'img', header: 'IMG' },
				{ dataIndex: 'updated', header: 'UPDATED' },
				{ dataIndex: 'itemname', header: 'ITEMNAME', sortable: true },
				{ dataIndex: 'datasource', header: 'DATASOURCE' },
				{ dataIndex: 'rank', header: 'RANK', sortable: true }
			],
			prepareData: function(data){
					data.shortName	= Ext.util.Format.ellipsis(data.name, 10);
					data.shortItem	= Ext.util.Format.ellipsis(data.itemname, 20);
					data.shortDS		= Ext.util.Format.ellipsis(data.datasource, 15);
					data.mediumItem	= Ext.util.Format.ellipsis(data.itemname, 30);
					data.mediumDS		= Ext.util.Format.ellipsis(data.datasource, 25);
					data.fmtUpdated	= Ext.util.Format.date(data.updated, 'j-m H:i');
					return data;
			},
			tpl: new Ext.XTemplate(
				'<tpl for=".">',
					'<tpl if="img.length &gt; 0">',
						'<div class="thumb-wrap" id="{id}" style="float:left">',
						'<div class="thumb"><a href="{url}" target="_new"><img src="{img}" title="{name}" width="150" alt="{datasource}"></a></div>',
						'<span class="meta"><table width="125"><tr><td colspan="2" align="left">{shortDS}:{shortName}</td></tr><tr><td>{fmtUpdated}</td><td width="50" align="right"><img src="/webxtractor/public/img/layout/ranking_{rank}.gif"></td></tr></table></span></div>',
					'</tpl>',
					'<tpl if="img.length &lt; 1">',
						'<div class="thumb-wrap" id="{id}"><table width="100%"><tr>',
						'<td><a href="{url}" target="_new">{name}</a></td>',
						'<td width="160" nowrap>{mediumItem}</td>',
						'<td width="125" nowrap>{mediumDS}</td>',
						'<td width="75" nowrap>{fmtUpdated}</td>',
						'<td width="50" align="right"><img src="/webxtractor/public/img/layout/ranking_{rank}.gif" height="16"></td>',
						'</tr></table></div>',
					'</tpl>',
				'</tpl>'
			),
			listeners: {
				dblclick: function(dataView, index, node, e) {
					dataView.select(index, true);
					e.stopEvent();
					var win = new Ext.Window({
	          width: 850,
	          height: 800,
	          modal: 'true',
	          layout: 'fit',
	          title: dataView.getStore().getAt(index).get('name'),
	          items: [{
	            xtype: 'iframepanel',
	            id: 'view',
	            defaultSrc: dataView.getStore().getAt(index).get('url'),
	            loadMask: false
	          }],
					});
	        win.show();
				},
				contextmenu: function(dataView, index, node, e) {
					dataView.select(index, true);
					e.stopEvent();
					if(!window.contextmenu){
						window.contextmenu = new Ext.menu.Menu({
							id:'menu-ctx',
							items: [
							{ 
								id: 'menu-title', 
								disabled: true,
								text: window.contexttitle ? window.contexttitle : '0 items selected'
							},'-',
							{
								text: '<img src="/webxtractor/public/img/layout/ranking_1.gif" height="16">',
								scope: this,
								handler: function(b, e) {
									var a = dataView.getSelectedRecords();
									for (i = 0; i < a.length; i++) {
					    			a[i].set('rank', 1);
					    		}
					    		dataView.getStore().save();
								}
							},
							{
								text: '<img src="/webxtractor/public/img/layout/ranking_2.gif" height="16">',
								scope: this,
								handler: function(b, e) {
									var a = dataView.getSelectedRecords();
									for (i = 0; i < a.length; i++) {
					    			a[i].set('rank', 2);
					    		}
					    		dataView.getStore().save();
								}
							},
							{
								text: '<img src="/webxtractor/public/img/layout/ranking_3.gif" height="16">',
								scope: this,
								handler: function(b, e) {
									var a = dataView.getSelectedRecords();
									for (i = 0; i < a.length; i++) {
					    			a[i].set('rank', 3);
					    		}
					    		dataView.getStore().save();
								}
							},'-',
							{
								text: 'Show',
								scope: this,
								handler: function(b, e) {
									var a = dataView.getSelectedRecords();
									var iframes = [];
									for (i = 0; i < a.length; i++) {
										iframes.push({
											title: a[i].get('name'),
											layout: 'fit',
											border: true,
											items: [{
		                    xtype: 'iframepanel',
		                    id: 'view',
		                    defaultSrc: a[i].get('url'),
		                    loadMask: false
		                  }]
		               	});
									}
									var win = new Ext.Window({
	                  width: 800,
	                  height: 600,
	                  modal: 'true',
	                  layout: 'fit',
	                  title: 'Offers',
	                  items: [{
	                  	xtype: 'tabpanel',
	                  	title: 'Offers',
											region: 'center',
											margins: '5 5 5 0',
											tabPosition: 'bottom',
											activeTab: 0,
											border: false,
											items: [iframes]
	                  }]
									});
	                win.show();
								}
							}]
						});
					}
					window.contextmenu.showAt(e.getXY());
				},
				selectionchange: function(dataView, nodes) {
	  			var l = nodes.length;
	  			var s = l != 1 ? 's' : '';
	  			window.contexttitle = l + ' item' + s + ' selected';
	  			if (window.contextmenu) {
	  				window.contextmenu.findById('menu-title').setText(window.contexttitle);
	  			}
	    	}
			},
			multiSelect: true,
			overClass:'x-view-over',
			itemSelector: 'div.thumb-wrap'
		}],
		tbar:[{
			text: 'Search:'
		},
		{
    	xtype: 'textfield',
    	id: 'offer-data-query',
    	selectOnFocus: true,
    	width: 100,
    	listeners: {
				'render': {
					fn: function() {
						Ext.getCmp('offer-data-query').getEl().on('keyup', function() {
								Ext.getCmp('offer-paging').doLoad(0);
							}, 
							this, 
							{buffer:500}
						);
	        }, 
	        scope:this
	    	}
			}	
		}, 
		' ', 
		'-', 
		{
			text: 'Sort By:'
		}, 
		{
			id: 'sortSelect',
      xtype: 'combo',
      typeAhead: true,
      triggerAction: 'all',
      width: 100,
      editable: false,
      mode: 'local',
      displayField: 'desc',
      valueField: 'name',
      lazyInit: false,
      value: 'updated',
      store: new Ext.data.ArrayStore({
        fields: ['name', 'desc'],
	      data : [['updated', 'Last Updated'],['itemname', 'Item'],['rank', 'Ranking']]
	    }),
	    listeners: {
				'select': {
					fn: function() {
						var v = Ext.getCmp('sortSelect').getValue();
						Ext.getCmp('offer-data-view').getStore().sort(v, v == 'itemname' ? 'asc' : 'desc');
					},
					scope:this
				}
	    }
    },
    ' ',
    '-',
    {
    	xtype: 'checkbox',
    	id: 'offer-data-new-cb',
    	boxLabel: 'only new',
    	checked: true,
    	listeners: {
				'check': {
					fn: function (field, val, oldval) {
						Ext.getCmp('offer-paging').doLoad(0);
					}
				}
			}
    },
    ' ',
    '-',
    {
    	text: 'Rank:'
    },
    {
    	xtype: 'slider',
    	id: 'offer-data-slider',
	    width: 80,
	    increment: 1,
	    minValue: 0,
	    maxValue: 3,
	    plugins: new Ext.ux.SliderTip({
				getText: function(slider){
					switch (slider.value) {
						case 0: return '<img src="/webxtractor/public/img/layout/ranking_0.gif">';
						case 1: return '<img src="/webxtractor/public/img/layout/ranking_1.gif">';
						case 2: return '<img src="/webxtractor/public/img/layout/ranking_2.gif">';
						case 3: return '<img src="/webxtractor/public/img/layout/ranking_3.gif">';
						default : return '';
					}
				}
			}),
			listeners: {
				'change': {
					fn: function (field, val, oldval) {
						Ext.getCmp('offer-data-new-cb').setValue(false);
						Ext.getCmp('offer-paging').doLoad(0);
					}
				}
			}
    },' ',
    {
    	xtype: 'checkbox',
    	id: 'offer-data-rank-cb',
    	boxLabel: 'and up',
    	checked: true,
    	listeners: {
				'check': {
					fn: function (field, val, oldval) {
						Ext.getCmp('offer-paging').doLoad(0);
					}
				}
			}
    }],
		bbar: new Ext.PagingToolbar({
			id: 'offer-paging',
			pageSize: 10,
			store: offerStore,
			items: [' ', '-', 
			{ 
				text: 'Per page: '
			},
			{
				xtype: 'textfield',
				id: 'offer-paging-perpage',
  			selectOnFocus: true,
  			width: 30,
  			listeners: {
					'render': {
						fn: function() {
							Ext.getCmp('offer-paging-perpage').getEl().on('keyup', function() {
									Ext.getCmp('offer-paging').pageSize = parseInt(Ext.getCmp('offer-paging-perpage').getValue());
									Ext.getCmp('offer-paging').doLoad(0);
								}, 
								this, 
								{buffer:500}
							);
        		}, 
        		scope:this
    			}
				}
			}]
		})
	};
}