<?php
/**
 * Compojoom Control Center
 * @package Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All rights reserved
 * @Joomla! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 0.9.0 beta $
 **/

// No direct access.
defined('_JEXEC') or die;

/**
 * Based on mod_feed
 * @package		Joomla.Administrator
 * @subpackage	mod_feed
 */
abstract class modCCCNewsfeedHelper
{
    public static function render($params)
    {
        // module params
        $rssurl				= $params->get('feedurl', '');
        $rssitems			= $params->get('feeditems', 5);
        $rssdesc			= $params->get('rssdesc', 1);
        $rssimage			= $params->get('rssimage', 1);
        $rssitemdesc		= $params->get('rssitemdesc', 1);
        $words				= $params->def('word_count', 0);
        $rsstitle			= $params->get('rsstitle', 1);
        $rssrtl				= $params->get('rssrtl', 0);
        $moduleclass_sfx	= $params->get('moduleclass_sfx', '');

        $filter = JFilterInput::getInstance();

        //  get RSS parsed object
        $rssDoc = JFactory::getFeedParser($rssurl);

//	    var_dump($rssDoc);
        // echo "feed " + $rssurl;

        if ($rssDoc != false)
        {
            // channel header and link
            $channel['title'] = $filter->clean($rssDoc->get_title());
            $channel['link'] = $filter->clean($rssDoc->get_link());
            $channel['description'] = $filter->clean($rssDoc->get_description());

            // channel image if exists
            $image['url'] = $rssDoc->get_image_url();
            $image['title'] = $rssDoc->get_image_title();



            //image handling
            $iUrl	= isset($image['url']) ? $image['url'] : null;
            $iTitle = isset($image['title']) ? $image['title'] : null;

            // items
            $items = $rssDoc->get_items();

            // feed elements
            $items = array_slice($items, 0, $rssitems);
            ?>
        <table cellpadding="0" cellspacing="0" class="moduletable<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
            <?php
            // feed description
            if (!is_null($channel['title']) && $rsstitle) {
                ?>
                <tr>
                    <td>
                        <strong>
                            <a href="<?php echo htmlspecialchars(str_replace('&', '&amp;', $channel['link'])); ?>" target="_blank">
                                <?php echo htmlspecialchars($channel['title']); ?></a>
                        </strong>
                    </td>
                </tr>
                <?php
            }

            // feed description
            if ($rssdesc) {
                ?>
                <tr>
                    <td>
                        <?php echo $channel['description']; ?>
                    </td>
                </tr>
                <?php
            }

            // feed image
            if ($rssimage && $iUrl) {
                ?>
                <tr>
                    <td align="center">
                        <img src="<?php echo htmlspecialchars($iUrl); ?>" alt="<?php echo htmlspecialchars(@$iTitle); ?>"/>
                    </td>
                </tr>
                <?php
            }

            $actualItems = count($items);
            $setItems = $rssitems;

            if ($setItems > $actualItems) {
                $totalItems = $actualItems;
            } else {
                $totalItems = $setItems;
            }
            ?>
            <tr>
                <td>
                    <ul class="newsfeed<?php echo htmlspecialchars($moduleclass_sfx); ?>"  >
                        <?php
                        for ($j = 0; $j < $totalItems; $j ++)
                        {
                            $currItem = & $items[$j];
                            // item title
                            ?>
                            <li>
                                <?php
                                if (!is_null($currItem->get_link())) {
                                    ?>
                                    <a href="<?php echo htmlspecialchars($currItem->get_link()); ?>" target="_child">
                                        <?php echo htmlspecialchars($currItem->get_title()); ?></a>
                                    <?php
                                }

                                // item description
                                if ($rssitemdesc)
                                {
                                    // item description
                                    $text = $filter->clean(html_entity_decode($currItem->get_description(), ENT_COMPAT, 'UTF-8'));
                                    $text = str_replace('&apos;', "'", $text);

                                    // word limit check
                                    if ($words) {
                                        $texts = explode(' ', $text);
                                        $count = count($texts);
                                        if ($count > $words) {
                                            $text = '';
                                            for ($i = 0; $i < $words; $i ++)
                                            {
                                                $text .= ' '.$texts[$i];
                                            }
                                            $text .= '...';
                                        }
                                    }
                                    ?>
                                    <div style="text-align: <?php echo $rssrtl ? 'right': 'left'; ?> !important">
                                        <?php echo $text; ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </td>
            </tr>
        </table>
        <?php
        }
    }
}