<?php
/**
* admin pages class and generator
 */
$options_panel = new ba_SubPage('settings', array('page_title' => __('Instant Search','sis'),'option_group' => 'baIS_settings'));
$options_panel->OpenTabs_container('');
$options_panel->TabsListing(array(
  'links' => array(
    'options' =>  __('Styling Options'),
    'options1' =>  __('Help')
    )
  ));
//options page
$options_panel->OpenTab('options');
$options_panel->addSubtitle(__('Options:','sis'));
$options_panel->addInput(array(
        'id' => 'limit',
        'label' => 'Results limit',
        'desc' => 'Limit the number or results returned by the search, less means faster',
        'standard' => '10'
      ));
$options_panel->addCheckbox(array(
    'id' => 'builtin',
    'label' => __('use Built-in StyleSheet?'),
    'desc' => __('if checked the plugin will include the default CSS, uncheck to use your own','sis'),
    'standard' => true,
  ));
$args=array(
  'public'   => true
); 
$output = 'names'; // names or objects, note names is the default
$operator = 'and'; // 'and' or 'or'
$post_types=get_post_types($args,$output,$operator); 

foreach ($post_types  as $post_type ) {
  $pt[] = $post_type;
}
$options_panel->addCheckboxArray(array(
      'id' => 'ptype',
      'label' => __('Select post types to include','sis'),
      'desc' => __('selected post types will be included in the search query','sis'),
      'standard' => false,
    'options' => $pt
    ));
$preview_url = plugins_url('simple-instant-search/images/');
$options_panel->addImageRadiobuttons(array(
    'id' => 'Loader_Image',
    'label' => 'Loader Image',
    'standard' => '4',
    'directory_url' => $preview_url,
    'options' => array(
      'preview_004.gif' => '4',
      'preview_005.gif' => '5',
      'preview_006.gif' => '6',
      'preview_007.gif' => '7',
      'preview_008.gif' => '8',
      'preview_009.gif' => '9',
    ),
  ));
$options_panel->CloseDiv_Container();
//help
$options_panel->OpenTab('options1');
$options_panel->addSubtitle(__('Help:','sis'));
$options_panel->addParagraph('<table border="0"><tr><td>
<h3>Custom Styling:</h3><p>you can style on your own using css:</p>
<ul>
  <li>Results container - #results{}</li>
  <li>Result item container - .result{}</li>
  <li>Results Title -.result h2 a {}</li>
  <li>Read More link - .readMore{}</li>
</ul>
<h3>Custom Output:</h3>
<p>This plugin has its own action filters and hooks so you can customize the output.<br>
Filter hooks:</p>
<table border="1" cellpadding="5" cellspacing="1" style="color #fff;">
  <tbody>
    <tr>
      <td style="text-align: center; font-size: 18px;color: #fff; background-color: rgb(0, 102, 102);">
        Filter tag</td>
      <td style="text-align: center; font-size: 18px;color: #fff;background-color: rgb(0, 102, 102);">
        Use</td>
      <td style="text-align: center;font-size: 18px;color: #fff; background-color: rgb(0, 102, 102);">
        Parameters</td>
    </tr>
    <tr>
      <td style="background-color: rgb(102, 153, 102);">
        <span style="color:#ffffff;">instant_search_q_query</span></td>
      <td style="background-color: rgb(102, 153, 102);">
        <span style="color:#ffffff;">Modify the query object<br />
        (exclude,include whatever)</span></td>
      <td style="background-color: rgb(102, 153, 102);">
        <span style="color:#ffffff;">$query as WP_Query object</span></td>
    </tr>
    <tr>
      <td style="background-color: rgb(51, 102, 153);">
        <span style="color:#ffffff;">instant_search_q_post_types</span></td>
      <td style="background-color: rgb(51, 102, 153);">
        <span style="color:#ffffff;">Modify the Post type</span></td>
      <td style="background-color: rgb(51, 102, 153);">
        <span style="color:#ffffff;">$post_types as array of<br />
        (post_type_names)</span></td>
    </tr>
    <tr>
      <td style="background-color: rgb(204, 0, 0);">
        <span style="color:#ffffff;">instant_search_q_orderby<span></td>
      <td style="background-color: rgb(204, 0, 0);">
        <span style="color:#ffffff;">Modify the orderby cluse of the query</span></td>
      <td style="background-color: rgb(204, 0, 0);">
        <span style="color:#ffffff;">$orderby as string</span></td>
    </tr>
    <tr>
      <td style="background-color: rgb(204, 0, 153);">
        <span style="color:#ffffff;">instant_search_q_limit</span></td>
      <td style="background-color: rgb(204, 0, 153);">
        <span style="color:#ffffff;">limit the number of results by hook<br />
        (when using more then one form)</span></td>
      <td style="background-color: rgb(204, 0, 153);">
        <span style="color:#ffffff;">$number as string</span></td>
    </tr>
    <tr>
      <td style="background-color: rgb(204, 102, 51);">
        <span style="color:#ffffff;">instant_search_res_content</span></td>
      <td style="background-color: rgb(204, 102, 51);">
        <span style="color:#ffffff;">Modify the Content Returned<br />
        (add images, tags, categories ...)</span></td>
      <td style="background-color: rgb(204, 102, 51);">
        <span style="color:#ffffff;">$content as string<br />
        $post_id as integer</span></td>
    </tr>
    <tr>
      <td style="background-color: rgb(255, 153, 0);">
        <span style="color:#ffffff;">instant_search_res_title_link</span></td>
      <td style="background-color: rgb(255, 153, 0);">
        <span style="color:#ffffff;">Modify the title Returned</span></td>
      <td style="background-color: rgb(255, 153, 0);">
        <span style="color:#ffffff;">$title_link as string<br />
        $post_id as integer</span></td>
    </tr>
    <tr>
      <td style="background-color: #333333;">
        <span style="color:#ffffff;">instant_search_res</span></td>
      <td style="background-color: #333333;">
        <span style="color:#ffffff;">Modify the results array<br />
        return whatever you want, Tags,Cats...)</span></td>
      <td style="background-color: #333333;">
        <span style="color:#ffffff;">$results as array of<br />
        (Title, Content, URL)</span>
      </td>
    </tr>
  </tbody>
</table>
</td><td style="vertical-align: top;">
<ul style="list-style: square inside none; width: 300px; font-weight: bolder; padding: 20px; border: 2px solid; background-color: #FFFFE0; border-color: #E6DB55;>
  <li>
    Any feedback or suggestions are welcome at <a href="http://en.bainternet.info/?p=347">plugin homepage</a></li>
  <li>
    <a href="http://wordpress.org/tags/simple-instant-search?forum_id=10">Support forum</a> for help and bug submission</li>
  <li>
    Also check out <a href="http://en.bainternet.info/category/plugins">my other plugins</a></li>
  <li>
    And if you like my work <span style="color: #FC000D;">make a donation</span><br/>
    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PPCPQV8KA3UQA"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"></a>
  </li>
</ul>

</td></tr></table>');
$options_panel->CloseDiv_Container();
$options_panel->CloseDiv_Container();