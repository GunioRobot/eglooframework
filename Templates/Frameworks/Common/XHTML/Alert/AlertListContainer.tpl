<!--{* @Name:   Alert List Container
     * @Author: George Cooper (george.cooper@egloo.com)
     *
     * @Description: This template represents the default structural format for the
     * eGloo alert list container.  It is meant to be plugged into a larger template
     * structure and requires external CSS for styling and screen positioning.
     * Effort is made to make this file XHTML compliant for at least XHTML version
     * 1.0 Transitional.
     *
     * @Standalone: No
     * @Provides: Structural Markup, Unique Page Elements, CSS classes
     * @Caching: Partial
     * @Compatibility: XHTML 1.0 Transitional (Tested)
     *
     * @Provides (id) AlertList
     *
     * @Requires (class) clickable
     * @Requires (class) messageItemContainer
     * @Requires (class) messageListEdit
     * @Requires (class) messageListSummary
     * @Requires (class) messageListTitle
     * @Requires (class) thickbox
     *
     * @Token alertList (array) list of alerts with which to fill the container
     *}-->

<!--{* @id: AlertList
	 *
	 * @Description: This is the div that contains the alert list.
     *}-->
<div id="AlertList">

<!--{foreach name=Alerts from=$alertList item=currentAlert}-->
	<div class="messageItemContainer <!--{if $smarty.foreach.Alerts.index % 2 == 0}-->gray<!--{/if}-->">
		<div class="messageListTitle clickable"><!--{$currentAlert->getTitle()}--></div>
		<div class="messageListEdit clickable">
			<a href="/blog/editBlogEntry/&height=400&width=475&blogID=<!--{$currentAlert->getBlogID()}-->" class="thickbox" title="Edit Blog Entry">edit</a>
		</div>
		<div class="messageListSummary"><!--{$currentAlert->getContent()}--></div>
	</div>
<!--{/foreach}-->

</div>

<div>

<!--{foreach from=$alertList item=currentAlert}-->
	<div><!--{$currentAlert->getID()}--></div>
	<div><!--{$currentAlert->getName()}--></div>
<!--{/foreach}-->

</div>