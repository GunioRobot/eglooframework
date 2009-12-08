<div id="Center_1_Block">
    <!--{* Displays search navigation for the global/network/favorites/recommended filters *}-->
    <div id="Menu_1_Header12">
        <div id="Menu_Header_Break4" class="clickable">Suggest</div>
        <div id="Menu_Header_Break3" class="clickable">Favorite</div>
        <div id="Menu_Header_Break2" class="clickable">Network</div>
        <div id="Menu_Header_Break1" class="clickable">Global</div>            
    </div>
    <!--{* The overall containers for the top 30 *}-->
    <div id="Center_Sub_Column1">
    	<div id="pod-wrap">
			<div id="top-but-wrap">
				<a id="top-but" onclick="podButtonClicked(event);" href="#">&nbsp;</a>
			</div>
			<div id="pod-list-wrap">
				<div style="left: 0px; top: 0px;" class="pod-list"></div>
			</div>
			<div id="bot-but-wrap">
				<a id="bot-but" onclick="podButtonClicked(event);" href="#">&nbsp;</a>
			</div>
		</div>
    </div>

	<!--{counter name=columnCounter start=20 skip=-10 assign=resultSectionLowerDelim}-->

    <!--{section name=resultColumnLoopIndex loop=$resultColumns show=true}-->
        <div id="<!--{$resultColumns[resultColumnLoopIndex]->id}-->" class="<!--{$resultColumns[resultColumnLoopIndex]->style}-->">
		<!--{section name=resultColumnItemIndex loop=$resultColumnItemList start=$resultSectionLowerDelim step=1 max=10 show=true}-->
            		<div id="<!--{$resultColumnItemList[resultColumnItemIndex]->getID()}-->" class="top30">
		            	<div class="top30Number"><!--{$smarty.section.resultColumnItemIndex.index_next}-->:</div>
		            	<div id="<!--{$resultColumnItemList[resultColumnItemIndex]->getProfileID()}-->" class="top30Value clickable"
		            		onclick="grabProfileSummary('#DropDownContentSummary','<!--{$resultColumnItemList[resultColumnItemIndex]->getProfileID()}-->');">
<!--{*		            		onclick="$.ajax({type:'POST',url:( '/relationship/requestBiDirectionalRelationship/&relationshipType=Friends&profileID=<!--{$resultColumnItemList[resultColumnItemIndex]->getProfileID()}-->' ),dataType:'xml'});"> *}-->
		            		<!--{$resultColumnItemList[resultColumnItemIndex]->getUserName()}-->
		            	</div>
            		</div>
		<!--{/section}-->
		</div>
		<!--{counter name=columnCounter}-->
    <!--{/section}-->

    <div id="DropDownContentSummary">
        People News
    </div>
</div>
    <!--{* Divs containing navigation to search deeper into the 30 search results *}-->
<div id="DropDownBottomNavigation">
<!--{*    <div id="DropDownBottomNavJoinRankedButton">Join eGloo's </div> *}-->
    <div id="DropDownBottomNavHelpButton" class="clickable">Help</div>
    <div id="DropDownBottomNavPrivacyButton" class="clickable">Privacy</div>
    <div id="DropDownBottomNavLegalButton" class="clickable">Legal</div>
    <div id="DropDownBottomNavArrowLeft" class="clickable">◀</div>
    <div id="DropDownBottomNavResultBounds">1 - 30</div>
    <div id="DropDownBottomNavArrowRight" class="clickable">▶</div>
</div>

	<script type="text/javascript">	
	//<![CDATA[


function TreeNode(userObject) {
	this.userObject = userObject;
	this.children = [];
	this.parent;
}

TreeNode.prototype.toString = function () {
	var result = "treeNode [uo=" + this.userObject
			+ ", childCount=" + this.children.length;
	if (this.parent) {
		result += ", parent.uo=" + this.parent.userObject;
	}
	result += "]";
	return result;
};

TreeNode.prototype.add = function (newChild) {
	if (newChild.parent) {
		newChild.parent.remove(newChild);
	}
	newChild.parent = this;
	this.children.push(newChild);
};

TreeNode.prototype.remove = function (child) {
	this.children.remove(child);
	child.parent = null;
};

TreeNode.prototype.isLeaf = function (child) {
	return this.children.length == 0;
};


// First, make sure all browsers have necessary ECMAScript v3 
// and DOM Level 1 properties

/** @type undefined */
var undefined;

/** @type Object */
var Node = Node ? Node : {};
/** @type number */
Node.ELEMENT_NODE 					= 1;
/** @type number */
Node.ATTRIBUTE_NODE 				= 2;
/** @type number */
Node.TEXT_NODE 						= 3;
/** @type number */
Node.CDATA_SECTION_NODE 			= 4;
/** @type number */
Node.ENTITY_REFERENCE_NODE 			= 5;
/** @type number */
Node.ENTITY_NODE 					= 6;
/** @type number */
Node.PROCESSING_INSTRUCTION_NODE 	= 7;
/** @type number */
Node.COMMENT_NODE 					= 8;
/** @type number */
Node.DOCUMENT_NODE 					= 9;
/** @type number */
Node.DOCUMENT_TYPE_NODE 			= 10;
/** @type number */
Node.DOCUMENT_FRAGMENT_NODE 		= 11;
/** @type number */
Node.NOTATION_NODE 					= 12;

/**
 *	@constructor
 *	@param MouseEvent evt
 */
function Evt(evt) {
	this._evt 	 = evt ? evt : window.event;
	this._source = this._evt.currentTarget ? 
				   this._evt.currentTarget : this._evt.srcElement;
	this._x = evt.pageX ? evt.pageX : evt.clientX;
	this._y = evt.pageY ? evt.pageY : evt.clientY;
}

/**
 *	@returns Element
 */
Evt.prototype.getSource = function () {
	return this._source;
};

/**
 *	@returns void
 */
Evt.prototype.consume = function () {
	if (this._evt.stopPropagation) {
		this._evt.stopPropagation();
		this._evt.preventDefault();
	}
	this._evt.returnValue  = false;
	this._evt.cancelBubble = true;
};

/**
 *	@returns string
 */
Evt.prototype.toString = function () {
	return "Evt [ x = " + this._x + ", y = " + this._y + " ]";
};
		
/**
 *	@returns number
 */
Evt.prototype.getX = function () {
	return this._x;
};

/**
 *	@returns number
 */
Evt.prototype.getY = function () {
	return this._y;
};

/**
 *	@returns Point
 */
Evt.prototype.getPoint = function () {
	return new Point(this._x,this._y);
};


function buildTreeModel() {
	var java = new TreeNode("java");
	root = new TreeNode("root");

	var awt = new TreeNode("awt");
	awt.add(new TreeNode("color"));
	awt.add(new TreeNode("datatransfer"));
	awt.add(new TreeNode("dnd"));
	awt.add(new TreeNode("event"));
	awt.add(new TreeNode("font"));
	awt.add(new TreeNode("geom"));
	awt.add(new TreeNode("im"));
	awt.add(new TreeNode("image"));
	awt.add(new TreeNode("print"));
	
	java.add(awt);
	root.add(java);
	
	var javax = new TreeNode("javax");
	var swing = new TreeNode("swing");
	swing.add(new TreeNode("border"));
	swing.add(new TreeNode("colorchooser"));
	swing.add(new TreeNode("event"));
	swing.add(new TreeNode("filechooser"));
	var plaf = new TreeNode("plaf");
	plaf.add(new TreeNode("basic"));
	plaf.add(new TreeNode("metal"));
	plaf.add(new TreeNode("multi"));
	plaf.add(new TreeNode("synth"));
	swing.add(plaf);
	var text = new TreeNode("text");
	text.add(new TreeNode("html"));
	text.add(new TreeNode("rtf"));
	swing.add(text);
	var tree = new TreeNode("tree");
	swing.add(tree);

	tree.add(new TreeNode("MutableTreeNode"));
	tree.add(new TreeNode("RowMapper"));
	tree.add(new TreeNode("TreeCellEditor"));
	tree.add(new TreeNode("TreeCellRenderer"));
	tree.add(new TreeNode("TreeModel"));
	tree.add(new TreeNode("TreeNode"));
	tree.add(new TreeNode("TreeSelectionModel"));
	tree.add(new TreeNode("Classes "));
	tree.add(new TreeNode("AbstractLayoutCache"));
	tree.add(new TreeNode("AbstractLayoutCache.NodeDimensions"));
	tree.add(new TreeNode("DefaultMutableTreeNode"));
	tree.add(new TreeNode("DefaultTreeCellEditor"));
	tree.add(new TreeNode("DefaultTreeCellRenderer"));
	tree.add(new TreeNode("DefaultTreeModel"));
	tree.add(new TreeNode("DefaultTreeSelectionModel"));
	tree.add(new TreeNode("FixedHeightLayoutCache"));
	tree.add(new TreeNode("TreePath"));
	tree.add(new TreeNode("VariableHeightLayoutCache"));
	tree.add(new TreeNode("Exceptions "));
	tree.add(new TreeNode("ExpandVetoException"));

	javax.add(new TreeNode("mail"));
	javax.add(new TreeNode("xml"));
	javax.add(swing);
	root.add(javax);
	
	var org = new TreeNode("org");
	var xml = new TreeNode("xml");
	var sax = new TreeNode("sax");
	sax.add(new TreeNode("ext"));
	sax.add(new TreeNode("helpers"));
	xml.add(sax);
	org.add(xml);
	
	var w3c = new TreeNode("w3c");
	var dom = new TreeNode("dom");
	
	dom.add(new TreeNode("Attr"));
	dom.add(new TreeNode("CDATASection"));
	dom.add(new TreeNode("CharacterData"));
	dom.add(new TreeNode("Comment"));
	dom.add(new TreeNode("Document"));
	dom.add(new TreeNode("DocumentFragment"));
	dom.add(new TreeNode("DocumentType"));
	dom.add(new TreeNode("DOMConfiguration"));
	dom.add(new TreeNode("DOMError"));
	dom.add(new TreeNode("DOMErrorHandler"));
	dom.add(new TreeNode("DOMImplementation"));
	dom.add(new TreeNode("DOMImplementationList"));
	dom.add(new TreeNode("DOMImplementationSource"));
	dom.add(new TreeNode("DOMLocator"));
	dom.add(new TreeNode("DOMStringList"));
	dom.add(new TreeNode("Element"));
	dom.add(new TreeNode("Entity"));
	dom.add(new TreeNode("EntityReference"));
	dom.add(new TreeNode("NamedNodeMap"));
	dom.add(new TreeNode("NameList"));
	dom.add(new TreeNode("Node"));
	dom.add(new TreeNode("NodeList"));
	dom.add(new TreeNode("Notation"));
	dom.add(new TreeNode("ProcessingInstruction"));
	dom.add(new TreeNode("Text"));
	dom.add(new TreeNode("TypeInfo"));
	dom.add(new TreeNode("UserDataHandler"));
	dom.add(new TreeNode("Exceptions "));
	dom.add(new TreeNode("DOMException"));
	
	w3c.add(dom);
	org.add(w3c);
	
	root.add(org);
	currNode = root;
}

var podListWrap;
var topButton;
var botButton;
var currNode;
var root;
var oldList;
var newList;
var isReverse;

function main() {
	findElements();
	buildTreeModel();
	renderPod();
}

function findElements() {
	topButton 	= $("#top-but");
	botButton 	= $("#bot-but");
	podListWrap = $("#pod-list-wrap");
}

function renderPod() {
	_renderButtons();
	_renderList();
}

function _renderButtons() {
	var uo;
	if (currNode == root) {
		uo = "&nbsp;";
	} else {
		uo = "&laquo; " + currNode.userObject;
	}
	
	topButton.html( uo );
	botButton.html( uo );
}

function _renderList() {
	oldList = newList;
	newList = _createNewListNode();
	newList.html( _createNewListHtml() );
	podListWrap.append(newList);
	_animateLists();
}

function _createNewListNode() {
	result = $("<div></div>").addClass( "pod-list" );
	if (isReverse) {
		result.left( '-175px' ).top( '0px' );
	} else {
		result.left( '175px' ).top( '0px' );
	}
	return result;
}

function _createNewListHtml() {
	var kid;
	var html = "";
	for (var i = 0; i < currNode.children.length; i++) {
		kid = currNode.children[i];
		html += "<a href='#'";
		if (kid.isLeaf()) {
			html += " class='leaf'>";
		} else {
			html += " onclick='podNodeClicked(event);'>" 
		}
		html += kid.userObject + "<\/a>";
	}
	return html;
}

function _animateLists() {
	var x = $(newList).left();
	var condition;
	if (isReverse) {
		condition = (x <= 0);
		newX = x+16;
		if (oldList)
			oldX = $(oldList).left()+16;
	} else {
		condition = (x >= 0);
		newX = x-16;
		if (oldList)
			oldX = $(oldList).left()-16;
	}
	if (condition) {
		$(newList).left(newX);
		if (oldList) {
			$(oldList).left(oldX);
		}
		setTimeout("_animateLists()",1);
	} else {
		$(newList).left(0);
		_removeOldList();
	}
}

function _removeOldList() {
	if (oldList) {
		oldList.remove();
	}
}

function podNodeClicked(evt) {
	evt = new Evt(evt);
	var index = _indexOfClickedNode(evt);
	currNode = currNode.children[index];
	isReverse = false;
	renderPod();
}

function _indexOfClickedNode(evt) {
	var source = evt.getSource();
	var nodes  = $(newList).find("a");
	return nodes.index(source);
}

function podButtonClicked(evt) {
	evt = new Evt(evt);
	if (currNode == root) {
		return;
	} else {
		currNode = currNode.parent;
	}
	isReverse = true;
	renderPod();
}

main();

</script>