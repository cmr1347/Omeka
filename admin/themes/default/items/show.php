<?php    
    $itemTitle = strip_formatting(item('Dublin Core', 'Title'));
    if ($itemTitle != '' && $itemTitle != __('[Untitled]')) {
        $itemTitle = ': &quot;' . $itemTitle . '&quot; ';
    } else {
        $itemTitle = '';
    }
    $itemTitle = __('Item #%s', item('id')) . $itemTitle;
?>
<?php head(array('title' => $itemTitle, 'bodyclass'=>'items show primary-secondary')); ?>

<?php echo js('items'); ?>

<h1 id="item-title"><?php echo $itemTitle; ?> <span class="view-public-page">[ <a href="<?php echo html_escape(public_uri('items/show/'.item('id'))); ?>"><?php echo __('View Public Page'); ?></a> ]</span></h1>

<?php if (has_permission($item, 'edit')): ?>
<p id="edit-item" class="edit-button"><?php 
echo link_to_item(__('Edit this Item'), array('class'=>'edit'), 'edit'); ?></p>   
<?php endif; ?>

<ul class="item-pagination navigation group">
<li id="previous-item" class="previous">
    <?php echo link_to_previous_item(); ?>
</li>
<li id="next-item" class="next">
    <?php echo link_to_next_item(); ?>
</li>
</ul>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
jQuery(document).ready(function () {
    Omeka.Items.modifyTagsShow();
    Omeka.Items.tagDelimiter = <?php echo js_escape(get_option('tag_delimiter')); ?>;
    Omeka.Items.tagChoices('#tags-field', <?php echo js_escape(uri(array('controller' => 'tags', 'action' => 'autocomplete'), 'default')); ?>);
});
//]]>     
</script>
<div id="primary">
<?php echo flash(); ?>

<?php if (item_has_files()): ?>
<?php
require_once HELPER_DIR . '/Media.php';
$media = new Omeka_View_Helper_Media;
$item = get_current_item();
?>
<script type="text/javascript">
jQuery(document).ready(function () {
  jQuery('.toggle-file-container').hide();
  jQuery('#show-files').click(function(e) {
    e.preventDefault();
    jQuery('.toggle-file-container').show('fast');
  });
  jQuery('#hide-files').click(function(e) {
    e.preventDefault();
    jQuery('.toggle-file-container').hide('fast');
  });
  jQuery('.toggle-file').click(function(e) {
    e.preventDefault();
    var file_id = parseInt(jQuery(this).closest('li').attr('id').split('-')[1], 10);
    jQuery('#toggle-file-' + file_id).toggle('fast');
  });
});
</script>
<div id="item-images">
    <h2>Files</h2>
    <p><a href="" id="show-files">show all</a> | <a href="" id="hide-files">hide all</a></p>
    <ol style="list-style: decimal inside;">
        <?php foreach ($item->Files as $file): ?>
        <li id="file-<?php echo $file->id; ?>" style="margin-bottom: 10px;">
            <a href="" class="toggle-file"><img src="<?php echo img('silk-icons/arrow_down.png'); ?>" /><?php echo $file->original_filename; ?></a> (<?php echo $file->mime_browser; ?>)
            <div id="toggle-file-<?php echo $file->id; ?>" class="toggle-file-container" style="margin: 10px 0 20px;"><?php echo $media->media($file, array('imageSize' => 'fullsize', 'linkText' => '[download file]'), array('class'=>'item-file')); ?></div>
        </li>
        <?php endforeach; ?>
    </ol>
</div>
<?php endif; ?>

<div id="core-metadata" class="showitem">
<?php echo show_item_metadata(array('show_empty_elements' => true)); ?>
</div>

<?php fire_plugin_hook('admin_append_to_items_show_primary', $item); ?>

</div>
<div id="secondary">
    
    <div class="info-panel">
        <h2><?php echo __('Bibliographic Citation'); ?></h2>
        <div>
            <p><?php echo item_citation();?></p>
        </div>
    </div>
    
        <div id="collection" class="info-panel">
        <h2><?php echo __('Collection'); ?></h2>
            <div>
               <p><?php if (item_belongs_to_collection()) echo item('Collection Name'); else echo __('No Collection'); ?></p>
            </div>
        </div>
    
    <div id="tags" class="info-panel">
        <h2><?php echo __('Tags'); ?></h2>
        <div id="tag-cloud">
            <?php common('tag-list', compact('item'), 'items'); ?>
        </div>
        
        <?php if ( has_permission('Items','tag') ): ?>
        
        <h3><?php echo __('My Tags'); ?></h3>
        <div id="my-tags-show">        
            <form id="tags-form" method="post" action="<?php echo html_escape(uri('items/modify-tags/')) ?>">
                <div class="input">
                    <input type="hidden" name="id" value="<?php echo item('id'); ?>" id="item-id" />
                    <input type="text" class="textinput" name="tags" id="tags-field" value="<?php echo tag_string(current_user_tags_for_item()); ?>" />
                </div>
                <p id="add-tags-explanation">Separate tags with <?php echo settings('tag_delimiter'); ?></p>
                <div>
                    <input type="submit" class="submit" name="modify_tags" value="<?php echo __('Save Tags'); ?>" id="tags-submit" />
                </div>
            </form>
        </div>
        
        <?php endif; ?>
        
    </div>
    
    <div class="info-panel">
        <h2><?php echo __('View File Metadata'); ?></h2>
            <div id="file-list">
                <?php if(!item_has_files()):?>
                    <p><?php echo __('There are no files for this item yet.');?> <?php echo link_to_item(__('Add a File'), array(), 'edit'); ?>.</p>
                <?php else: ?>
                    <ul>
                        <?php while(loop_files_for_item()): ?>
                            <li><?php echo link_to_file_metadata(array('class'=>'show', 'title'=>__('View File Metadata'))); ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif;?>
            </div>
    </div>

    <div class="info-panel">
        <h2><?php echo __('Output Formats'); ?></h2>
        <div><?php echo output_format_list(); ?></div>
    </div>
    
    <?php fire_plugin_hook('admin_append_to_items_show_secondary', $item); ?>
</div>
<?php foot();?>
