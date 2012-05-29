<!-- BEGIN ROOT_LINK -->
<a href="{URL}" {ATTRIBUTES}>{TEXT}</a>
{SUBTREE}
<!-- END ROOT_LINK -->

<!-- BEGIN ROOT_NOLINK -->
<span {ATTRIBUTES}>{TEXT}</span>
{SUBTREE}
<!-- END ROOT_NOLINK -->

<!-- BEGIN TREE -->
<ul {TREE_ATTRIBUTES}>
	{LEAFS}
</ul>
<!-- END TREE -->

<!-- BEGIN LEAF_LINK -->
<li>
	<a href="{LEAF_URL}" {LEAF_ATTRIBUTES}>{LEAF_TEXT}</a>
	{SUBTREE}
</li>
<!-- END LEAF_LINK -->

<!-- BEGIN LEAF_NOLINK -->
<li>
	<span {LEAF_ATTRIBUTES}>{LEAF_TEXT}</span>
	{SUBTREE}
</li>
<!-- END LEAF_NOLINK -->