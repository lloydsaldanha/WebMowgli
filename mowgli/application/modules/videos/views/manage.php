<script>

	var DURATION_slide=800;
	init=function(){

		wm.debug('vid manage: init called!');

		// @Lloyd : handle Delete button click
		$('#video_playlist a.video-delete-button').click(function(e){

			e.preventDefault();

			var songObj = $(this);
			var itemId = songObj.attr('callback_id');

			//wm.debug('Delete button clicked : id = ' + itemId);

			confirm_action({

				message : "Permanently delete video ?",
				success : function(){

					wm.admin.utility.ajax.post({

						url : songObj.attr('href'),
						on_success : function(){

							$('li[vid_id='+itemId+']').hide(1500).remove();

						}

					});
				}
			});
		});


		makeVidListSortable();
		var v_left=($('.draghandle:first').width()-$('.draghandle:first .drag_tooltip').width())/2;
		$('.draghandle .drag_tooltip').css({left:v_left});

		$('div.expand_button a[type=js_container_toggler]').bind('click',function(event){
			event.stopPropagation();
			if($(this).html()=='+'){$(this).html('-').attr('title','Hide description');$(this).parents('.video_item').children('.vid_container').slideDown(DURATION_slide);}
			else{$(this).html('+').attr('title','Show description');$(this).parents('.video_item').children('.vid_container').slideUp(DURATION_slide);}
			return false;
		})

	}
	var lastDroppedVideo,sort_start_vid_Id;
	function makeVidListSortable(){

		$("ul#video_playlist").sortable({
			handle: 'div.draghandle',
			revert: true,
			start: function(event, ui){
				wm.debug('sortable> start');
				//ev=ui;

				sort_start_vid_Id = $(ui.helper).attr('vid_id');
				lastDroppedVideo =sort_start_vid_Id;
				depthBeforeDrag=getDepthDetails('video',sort_start_vid_Id);
				wm.debug('depthBeforeDrag.depth:'+depthBeforeDrag.depth+',vid_id:'+sort_start_vid_Id)

				//Make the z-index higher than properties page
				wm.debug('start moving vid_id'+sort_start_vid_Id);
				wm.debug('sortable> start [END]');
			},
			stop: function(event, ui) {
				wm.debug('sortable> stop[done sorting]');
				var new_id,change=true;
				var depthAfterDrag=getDepthDetails('video',sort_start_vid_Id);
				if(depthAfterDrag.found) {
					if( depthBeforeDrag.depth < depthAfterDrag.depth){
						//downward
						new_id=depthAfterDrag.prevId;
					} else if( depthBeforeDrag.depth > depthAfterDrag.depth){
						//upward
						new_id=depthAfterDrag.nextId;
					} else if(depthBeforeDrag.depth==depthAfterDrag.depth){
						change=false;
					}
					wm.debug('vid depthBeforeDrag.depth:'+depthBeforeDrag.depth+',depthAfterDrag.depth:'+depthAfterDrag.depth+',sort_start_vid_Id:'+sort_start_vid_Id);
					wm.debug('vid new_id:'+new_id+',old id:'+lastDroppedVideo+',change:'+change+',depthAfterDrag.found:'+depthAfterDrag.found);
					if(change){
						wm.debug('postSortOrder>');
						postSortOrder(lastDroppedVideo,new_id,wm.get_url(video_post_uri['video']['sort_order']));
					}
				}
			} ,
			change: function(event, ui) { wm.debug('sortable> change')},
			update:function(event, ui) { wm.debug('update sorting')}
		});
	}

	var video_post_uri = {
		video: {
			sort_order:'admin/videos/reorder_videos/'
		}
	}
</script>
<style>
	#video_playlist {
		list-style: none;
	}
	.video_item {
		background: none repeat scroll 0 0 #FFFFFF;
		border: 1px solid #D6D6D6;
		border-radius: 13px 13px 13px 13px;
		margin: 0 0 15px;
		padding: 8px;
	}

	.draghandle {
		background: -moz-linear-gradient(center top , #FFFFFF 0%, #F2F2E8 100%) repeat scroll 0 0 transparent;
		border: 1px solid #DFDFDF;
		border-radius: 3px 3px 3px 3px;
		box-shadow: 0 2px 2px #D6D6D6;
		cursor: move;
		display: block;
		font-size: 16px;
		height: 25px;
		margin: 0 0 10px;
		padding: 12px 0 0 10px;
		position: relative;
	}

	.draghandle .drag_tooltip {
		background: none repeat scroll 0 0 white;
		border: 1px solid #437DF7;
		color: blue;
		display: none;
		font-size: 10px;
		padding: 3px;
		position: absolute;
		right: 20px;
		top: -18px;
		width: 75px;
	}

	.draghandle:hover .drag_tooltip{ display:block}

	.vid_title_label{
		font-size: 13px;
		color:#828080;
	}
	.vid_title{
		color: #5C5C5C;
		margin: 0 0 0 6px;
		text-shadow: 0 1px 2px #FCFCFC;
	}

	.menu-button-item-blue{
		border-radius: 14px 14px 14px 14px;
		min-height: 13px;
		padding: 6px 6px 4px;
		width: 43px;
		float: right;
		margin:-5px 5px 0 0;
	}


	.left_col{
		display: block;
		float: left;
		height: auto;
		width: 150px;
	}
	.left_col img{
		border: 3px solid #FFFFFF;
		box-shadow: 0 3px 3px 2px #CFCFCF;
		color: red;
		margin: 10px 0 0 10px;
	}
	.right_col{
		float: left;
		width: 80%;
	}

	.ref_id,.vid_url{
		color: #5F62FF;
	}
	.ref_id{
		font-weight: bold;
	}
	span.vid_label_{
		display: none;
		margin: 10px 0 3px;
	}
	.vid_script ,
	.vid_desc {
		border: 1px solid #EDEDED;
		color: #6D6D6D;
		font-size: 13px;
		height: 45px;
		overflow: auto;
		padding: 5px;
		width: 98%;
	}

	/*Over-writing*/
	.vid_script{ display:none}
	.vid_desc {height: 90px;}

	.vid_url_path_container {
		border: 1px solid #A4CDFF;
		color: #2D71C4;
		display: block;
		float: right;
		padding: 4px;
		text-decoration: none;
		margin:0 0 5px 0;
	}

	.vid_container{ display:none}

	.expand_button {
		float: right;
		font-size: 25px;
		margin: -7px 10px 0 15px;
	}

	.expand_button a {
		background-color: #4D90FE;
		background-image: -moz-linear-gradient(center top , #81B1FC, #4288F8);
		border: 1px solid #AAAAAA;
		border-radius: 5px 5px 5px 5px;
		box-shadow: 0 1px 0 rgba(255, 255, 255, 0.5) inset;
		color: #FAFAFA;
		cursor: pointer;
		display: inline-block;
		font-family: "Helvetica Neue","Helvetica",Arial,Verdana,sans-serif;
		font-size: 18px;
		font-weight: bold;
		line-height: 1;
		padding: 2px 6px;
		position: relative;
		text-align: center;
		text-decoration: none;
		text-shadow: 0 -1px 1px rgba(0, 0, 0, 0.28);
		width: 10px;
	}

	.expand_button a:hover {
		background-color: #375AE8;
		background-image: -moz-linear-gradient(center top , #4D90FE, #357AE8);
		border: 1px solid #3079ED;
		color:#FFF;
	}

	ul#video_playlist li.ui-sortable-helper{
		-moz-box-shadow: -3px 2px 6px #888888;
		box-shadow: -3px 2px 6px #888888;
	}

	.visible_1{opacity:1}
	.visible_0{ opacity:0.75}

</style>

<ul id="video_playlist">
	{videos}
	<li class="video_item visible_{video:is_visible}" vid_id="{video:id}" order="{video:order}">
		<div class="draghandle">
			<span class="drag_tooltip">Drag to re-order</span>
			<span class="vid_title_label"><!-- Title --> </span><span class="vid_title">{video:title}</span>
			<div class="expand_button"><a type="js_container_toggler" href="#">+</a></div>
			<a class="menu-button-item-blue" href="{video:edit_link}" >Edit</a>
			<a type="inplace" callback_id="{video:id}" target="_self" class="video-delete-button menu-button-item-blue" href="{video:delete_link}" >Delete</a><br/><br/>
		</div>
		<!-- pending>{video:image_url} -->
		<div class="vid_container">
			<div class="left_col">
				<a class="vid_url_container" href="{video:image_url}" title="Preview" target="_blank"><img src="{video:image_url}" alt="No Image Selected"/></a>
			</div>
			<div class="right_col">
			    <a class="vid_url_path_container" href="http://youtu.be/{video:ref_id}" title="Preview" target="_blank"><div class="vid_url_path"> preview video<!-- <span class="vid_url">youtu.be/</span><span class="ref_id">{video:ref_id}</span> --></div></a>
				<span class="vid_label_">Description</span>
				<div class="vid_desc">{video:description}</div>
				<span class="vid_label_">Embed Script</span>
				<textarea class="vid_script" name="script" rows="2" cols="30">{video:script}</textarea>
			</div>
			<br class="clear" />
		</div>
	</li>
	{/videos}
</ul>

