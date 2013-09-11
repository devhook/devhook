<? $columns = 18 ?>
<?php $all_icons = array(
'Web application'    => array('adjust','anchor','archive','asterisk','ban-circle','bar-chart','barcode','beaker','beer','bell','bell-alt','bolt','book','bookmark','bookmark-empty','briefcase','bug','building','bullhorn','bullseye','calendar','calendar-empty','camera','camera-retro','certificate','check','check-empty','check-minus','check-sign','circle','circle-blank','cloud','cloud-download','cloud-upload','code','code-fork','coffee','cog','cogs','collapse','collapse-alt','collapse-top','comment','comment-alt','comments','comments-alt','compass','credit-card','crop','dashboard','desktop','download','download-alt','edit','edit-sign','ellipsis-horizontal','ellipsis-vertical','envelope','envelope-alt','eraser','exchange','exclamation','exclamation-sign','expand','expand-alt','external-link','external-link-sign','eye-close','eye-open','facetime-video','female','fighter-jet','film','filter','fire','fire-extinguisher','flag','flag-alt','flag-checkered','folder-close','folder-close-alt','folder-open','folder-open-alt','food','frown','gamepad','gear','gears','gift','glass','globe','group','hdd','headphones','heart','heart-empty','home','inbox','info','info-sign','key','keyboard','laptop','leaf','legal','lemon','level-down','level-up','lightbulb','location-arrow','lock','magic','magnet','mail-forward','mail-reply','mail-reply-all','male','map-marker','meh','microphone','microphone-off','minus','minus-sign','minus-sign-alt','mobile-phone','money','moon','move','music','off','ok','ok-circle','ok-sign','pencil','phone','phone-sign','picture','plane','plus','plus-sign','plus-sign-alt','power-off','print','pushpin','puzzle-piece','qrcode','question','question-sign','quote-left','quote-right','random','refresh','remove','remove-circle','remove-sign','reorder','reply','reply-all','resize-horizontal','resize-vertical','retweet','road','rocket','rss','rss-sign','screenshot','search','share','share-alt','share-sign','shield','shopping-cart','sign-blank','signal','signin','signout','sitemap','smile','sort','sort-by-alphabet','sort-by-alphabet-alt','sort-by-attributes','sort-by-attributes-alt','sort-by-order','sort-by-order-alt','sort-down','sort-up','spinner','star','star-empty','star-half','star-half-empty','star-half-full','subscript','suitcase','sun','superscript','tablet','tag','tags','tasks','terminal','thumbs-down','thumbs-down-alt','thumbs-up','thumbs-up-alt','ticket','time','tint','trash','trophy','truck','umbrella','unchecked','unlock','unlock-alt','upload','upload-alt','user','volume-down','volume-off','volume-up','warning-sign','wrench','zoom-in','zoom-out'),
'Currency Icons'     => array('bitcoin','btc','cny','dollar','eur','euro','gbp','inr','jpy','krw','renminbi','rupee','usd','won','yen'),
'Text Editor Icons'  => array('align-center','align-justify','align-left','align-right','bold','columns','copy','cut','eraser','file','file-alt','file-text','file-text-alt','font','indent-left','indent-right','italic','link','list','list-alt','list-ol','list-ul','paper-clip','paperclip','paste','repeat','rotate-left','rotate-right','save','strikethrough','table','text-height','text-width','th','th-large','th-list','underline','undo','unlink'),
'Directional Icons'  => array('angle-down','angle-left','angle-right','angle-up','arrow-down','arrow-left','arrow-right','arrow-up','caret-down','caret-left','caret-right','caret-up','chevron-down','chevron-left','chevron-right','chevron-sign-down','chevron-sign-left','chevron-sign-right','chevron-sign-up','chevron-up','circle-arrow-down','circle-arrow-left','circle-arrow-right','circle-arrow-up','double-angle-down','double-angle-left','double-angle-right','double-angle-up','hand-down','hand-left','hand-right','hand-up','long-arrow-down','long-arrow-left','long-arrow-right','long-arrow-up'),
'Video Player Icons' => array('backward','eject','fast-backward','fast-forward','forward','fullscreen','pause','play','play-circle','play-sign','resize-full','resize-small','step-backward','step-forward','stop','youtube-play'),
'Brand Icons'        => array('adn','android','apple','bitbucket','bitbucket-sign','bitcoin','btc','css3','dribbble','dropbox','facebook','facebook-sign','flickr','foursquare','github','github-alt','github-sign','gittip','google-plus','google-plus-sign','html5','instagram','linkedin','linkedin-sign','linux','maxcdn','pinterest','pinterest-sign','renren','skype','stackexchange','trello','tumblr','tumblr-sign','twitter','twitter-sign','vk','weibo','windows','xing','xing-sign','youtube','youtube-play','youtube-sign'),
'Medical Icons'      => array('ambulance','h-sign','hospital','medkit','plus-sign-alt','stethoscope','user-md'),
); ?>

<? Page::head('<style>
table.devhook-icons td {cursor:pointer}
table.devhook-icons td:hover {background-color:#EEE}
table.devhook-icons td.active {background-color:#3276b1 !important; color:#FFF;}
</style>') ?>

<?=Form::hidden($field, $value, $attr) ?>

<table id="<?=$attr['id'] ?>_table" class='devhook-icons table table-condensed table-bordered text-center'>
<? foreach ($all_icons as $group => $icons): ?>
	<tr class="active">
		<td colspan="<?=$columns ?>" class="text-center text-muted"><?=$group ?></td>
	</tr>
	<tr>
	<? foreach ($icons as $i => $icon): ?>
		<? if ($i && $i%$columns == 0): ?></tr><tr><? endif ?>
		<? $active = $icon == $value ?>
		<td<?=$active ? ' class="active"' : '' ?>><i class="icon-<?=$icon ?> icon-large"></i></td>
	<? endforeach ?>
	</tr>
<? endforeach ?>
</table>

<script>
$("#<?=$attr['id'] ?>_table td").click(function(){
	var icon = $(this).find('i').attr('class').replace(/^icon-([^ ]+).*$/, '$1');
	$("#<?=$attr['id'] ?>_table td.active").removeClass('active');
	$(this).addClass('active');
	$("#<?=$attr['id'] ?>").val(icon);
});
</script>