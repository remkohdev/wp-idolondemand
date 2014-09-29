==DESCRIPTION==
WP-IDOLOnDemand is a WordPress plugin that adds the power of enterprise search 
to your WordPress site. IDOL is HP Autonomy's leading big data server for 
machine learning based analytics of unstructured data like text and images. 

For more information, check out http://...

Features include:
- Indexing Posts and Comments in IDOL for advanced text mining


==INSTALLATION== 
To install the plugin in WordPress, select the 'wp-idolondemand' folder and add 
it to a zip archive. In WordPress Plugins, go to Add New > Upload, and select 
the created zip file, e.g. 'wp-idolondemand-v1.0.zip' and follow the WordPress 
instructions.

FAQ 
SCREENSHOTS 
CHANGELOG 
STATS 
SUPPORT 
REVIEWS 
DEVELOPERS

 
==TODO==
BUGS
O activate/deactivate bug: The plugin generated 466 characters of unexpected 
  output during activation. If you notice 'headers already sent' messages, 
  problems with syndication feeds or other issues, try deactivating or removing 
  this plugin.
O debug bulk indexing for all unindexed posts

==v0.1.0==
NEXT:
O add asynchronous indexing
O track Job-ids for asynchronous processing
O refresh status of jobIDs onload page
O On status becomes not-publish: remove from index
O currently unique rows for settings are not enforced, this causes the user to 
  be able to insert an empty value for apikey. Add a non-null validation, unique 
  setting option, and delete button to remove apikey

ADMIN-INDEXES
O add auto-index posts config option, currently all posts are auto indexed onsave
  allow per post/page to not index/remove from index via meta-box

USER-POST-UI
O List related posts, compare WP default to IOD smart relations

==v0.2.0==
ADMIN
O on deactivate, uninstall

ADMIN-APIS
O add Sentiment Analysis to posts and pages, make this a config option
O add Sentiment Analysis to comments, make this a config option
O create a wp_idolondemand_get_analysis() function that returns configured API 
  data: sentiment, etc.
O add to post: related terms, similar posts, etc. make this a config option
O consider making sentiment a Custom Field or a WP method

ADMIN-INDEXES
O (META-BOX) Add 'indexed' and 'index' config options as Custom Fields to Post.
  On post and page edit screens add 'is_indexed','index this post' options to 
  meta-box
O maximize number of indexes to 1
O develop strategy for indexing/querying 'unpublished'/'invisible' posts
O option setting: only posts that are not indexed should be indexed by default
  when 'index all' is pressed

==v1.0.0==
First production ready release.

ADMIN
O Add unit tests: SeleniumHQ.org

PUBLISH
O Add plugin to WordPress Plugins site, http://www.wordpress.org/plugins/add



==v1.1.0==
O create separate indexes for WordPress MultiSite (PRO)
O implement index all published posts. O Index all posts: 
  currently not implemented, requires better performance, error 
  handling and logging (ability to track/retrieve what posts are indexed and 
  what not
O when you do an 'index all posts' output all indexed posts on new line, console 
  output
O create website connector, possibly replace indexing functions by connector (on
  install create connector, instead of index all)

ADMIN-INDEXES
O Remove or include HTML tags in text to index, as config option


