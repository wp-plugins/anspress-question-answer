=== AnsPress - Question and answer plugin ===
Contributors: nerdaryan, alexandruias, Zoker, peterolle, mertskaplan, chrintz, masterbip, sm_rasmy, monocat, KTS915, chadfullerton
Donate link: https://www.paypal.com/cgi-bin/webscr?business=rah12@live.com&cmd=_xclick&item_name=Donation%20to%20AnsPress%20development
Tags: question, answer, q&a, forum, community, profile, stackoverflow, quora
Requires at least: 3.5.1
Tested up to: 4.1
Stable tag: 1.4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

AnsPress - Best question and answer plugin for WordPress. Made with developers in mind, highly customizable

== Description ==
Demo & support forum: http://wp3.in/questions/


= Looking for someone to customize AnsPress ? Hire us. =
Contact us at support@open-wp.com

Easily add question and answer section like stackoverflow.com or quora.com in your WordPress.
AnsPress is a most complete question and answer system for WordPress. AnsPress is made with developers in mind, highly customizable. AnsPress provide an easy to use override system for theme. List of features:

  * Sorting question and answer by many options.
  * Ajax based form submission
  * Theme system
  * Flag and moderate posts
  * Voting on question and answer
  * Question tags and categories
  * Question labels (i.e. Open, close, duplicate) new labels can be added easily.
  * Select best answer
  * Tags suggestions
  * Comment on question and answer
  * Private messaging system
  * Point based permission (under development)
  * reCaptcha
  * User level : Participant, Editor, Moderator (in future it can be customised and you can add your own levels)
  * Email notification
  * User can add questions to his favorite
  * User can edit profile
  * User can upload cover photo
  * Friends and followers system
  * User points system (reputation)
  * User ranking
  * User badge
  * User profile
  * Pages : Tags, categories, users etc.
  * SOCIAL LOGIN (see FAQ)
  * SYNTAX HIGHLIGHTER (see FAQ)

= Developers =

Project site: [Open-WP](http://wp3.in)
GitHub repo: [Git AnsPress](https://github.com/wp3/anspress/)
Twitter: [@openwp](http://twitter.com/wp3in)
GooglePlus: [@openwp](https://plus.google.com/+rahularyannerdaryan?rel=author)

= Help & Support =
For fast help and support, please post on our forum http://open-wp.com/questions/


**Page Shortcodes**

Use this shortcode in base to AnsPress work properly
`[anspress]`


== Installation ==

Its very easy to install the plugin.

* Install plugin (WP -> Plugins -> Install new -> Anspress)
* Activate plugin (Plugins -> Installed Plugins -> Activate)
* Create an AnsPress page (mine had done it automatically, but incase it doesnt) Pages -> Add New -> Type [anspress] in the page and publish (the name can be changed to whatever you like, but there must be `[anspress]` shortcode in content.
* Add AnsPress links in your menu, Appearance -> Menus, and then drag AnsPress links in your menu, and save.
* Your page should have the Ask, Categories, Tags, Users etc wherever you placed your menu!


= Page Template =

As AnsPress output its content based on shortcode, so you must edit you page template to avoid double title. So create a page template without title and set it for AnsPress base page. or simply create a page template with this name `page-YOUR_BASE_PAGE_ID.php`. Change the YOUR_BASE_PAGE_ID to the id of the base page.

That's all. enjoy :)


== Frequently Asked Questions ==

**Page Shortcodes**

`[anspress]` add anspress shortcode to your base page for anspress to work properly

= Can I override theme ? =

Yes, you can override the theme file easily. Simply follow below steps:

1. Create a `anspress` dir inside your currently active wordpress theme directory.
	
2. Copy file which you want to override from AnsPress theme dir to currently created dir in your WordPress theme folder.

**Social login**

For activating social login install [WordPress Social Login](http://wordpress.org/plugins/wordpress-social-login/ "WordPress Social Login") and activate it. Then add API keys of providers.
And thats all, rest of things will be handled by AnsPress.

**Syntax highlighter**

For activating social login install [Crayon Syntax Highlighter](http://wordpress.org/plugins/crayon-syntax-highlighter/ "Crayon Syntax Highlighter") and activate it.


== Screenshots ==

1. List of question in home page, category, or tags page.

2. Single page layout with selected answer

3. User profile

4. User's messages and conversations

5. Users page

6. Tags suggestion in ask form

7. Categories page with sub categories

8. Tags page

9. AnsPress options page

10. AnsPress admin menu

11. AnsPress labels

12. AnsPress auto installation page

13. User pages


== Changelog ==

= 1.4.3 =

* Made compatible with WordPress 4.1

= 1.4.1 =

*  Added conical url in head
*  [new] Added feed in wp_head #91 
*  [new]link to profile and tooltip for participants, [fix]show user login in edit profile , [new]Option for private question, [fix]ask button align
*  [fix] Anonymous name not working in answer #186
*  [fix] Label icon
*  [fix]cannot send question comment with disqus
*  Also hide unneeded space, when double title
*  [fix]disqus conflict with anspress
*  [fix]title is to the top of the page
*  [fix]title is to the top of the page
*  [fix]ask page title not disappearing
*  [fix] missing DIV
*  Add anspress class only if anspress base page
*  [added] override stylesheet
*  [fix] email not sent when answered
*  Added ko_KR and id language files
*  Option to hide the titles, sidebar style fix, fix input type
*  [fix] typo
*  sidebar stylefix
*  Move option
*  [fix] avatar not loading in comment #192
*  [fix] double email to admin when answer
*  [fix] No content loaded on question edit #183
*  added comment id for better seo

= 1.4 =

* [new] custom login URL, toggle login signup
* [fix] text domain
* [fix] added missing close icon in modal
* [fix] Structured Data missing format #148
* [fix] Undefined error if anonymous
* [fix] bootstrap-tagsinput.min.js.map missing #172
* Show anonymous name if submitted
* Anonymous answer posting
* [new] Anonymous question posting #170
* [fix] add default meta when create post from wp-admin #134
* Improved style of login, signup and social login
* Updated font icon
* Add ability to disable/enable login and signup
* [fix] favorite button
* Fix conflict with avatar outside anspress
* Store question and answer fields data in browser
* Added wordpress login from to prevent conflict
* Improved style of question page
* Removed new answer history from answer
* Hide change label icon if user cant change label
* Removed login backdrop
* Updated POT
* style fix, typo
* Skip reCaptcha based on user points
* Fix ReCaptha options
* Improved avatar resize
* Show the username of the user in Edit profile
* Added option for users_per_page
* Fix Allow anonymous option
* Fix Show signup form option
* Center anonymous box
* Fix sidebar
* fix author credit
* [new] Added anonymous
* Added icon function
* Added trigger for voting
* [new] Added options
* Added dynamic image resize 
* fix body question width, add cursor style
* Fix body width with no sidebar
* Fix users pagination
* Fix modal style, fix modal typo
* Added limit to question widget
* Added ap_icon
* Fix pagination issue
* Minor style fix
* Responsive Search Widget
* Better modal style
* body width
* Revert "Added realTime addon, for live notification"
* Removed realtime
* Minor spelling correction
* Added Chinese language
* Sanitized data
* [Fix] Status message overlay each other #133
* [New] X point needed to create a new tag, minimum numbers of tags
* Styled search suggestion
* Checkbox not saving 

= 1.3.1 =

* Checkboxes not saving data needs fix now #131 

= 1.2 =

* [Fix] PHP Notice on Menus page #139
* [Fix] Upload avatar button display above image #135
* [Fix] Conditional grammar fix in favorite button {538}
* [New] Add delete question button for question author {501}
* [Fix] Large images cause layout to break {568}
* fix style issue
* Responsive inserted images
* Increase the text-holder size for tags
* Increase the text-holder size for tags
* Fix tag style issue
* Fix tag style issue
* Wrong padding off question sort buttons on user question page, fix overlay issue
* Improved user links
* [fix] es_page to ap_page
* [new] Dynamically update menu links
* [fix] Hide updated message after 5 seconds
* [fix] Input type number not saving data needs fix now #130 {530}
* Fix private checkbox look , styling more stuffs , remove useless css
* Better handling of admin settings



= 1.1 =

* [fix] Vote undo {513}
* Moved pagination of following and followers page
* Fixed pagination in user following and follower page
* [fix] Failed to load /js/bootstrap-tagsinput.min.js.map #126
* [fix] View count not work for anonymous user #121 {398}
* Added before body widget 

= 1.0 =

* Added Arabic language, shared by shady2
* Fix typo
* Fix wrong message when submiting a question
* Reposition the comma for better alignment
* Fix un-clickable links
* Fix tag Arrow
* Add comma to separate tags
* Fix bug at following users
* #122 [Fix] External images cannot be inserted {446}
* Added post_discussion widget
* Added: Discussion on a post
* User page tab
* Added private_question chekbox in ask question form
* #104 Optionally add "Ask Question" button on top and bottom
* #118 Hide "edit", "close", "flag" link for guests
* removed duplicate history time
* Added label history to post
* Email notification on selecting best answer
* #74 Added private question
* Improved history
* Fixed email notification and list styles
* Added template function for checking if there is label, category and tag
* Fixed duplicate "last updated action"
* Improved list page SEO
* #116 "Unanswered" filter should mean "Unsolved"
* changed the file name of anspress-shortcodes.php to anspress-basepage
* Removed messaging system
* Fixed email addon, private messages and improved SEO of answers
* Admin can set prefix of question #67
* Comment notification #96
* Added default en_US language and Spanish language
* Improved email notification and added comment notification 
* Improved email notification
* Improved question page
* Fixed user capability on activate and improved question page
* Improved icon
* Improved question page layout.
* Quote button not adding markup #99
* Show cover page only in profile page
* Convert special characters inside `<pre>` to entity #98
* WP users with space break in their user names have missing profiles #95
* Add tooltip in list item counts #61
* Added alternate link in head #90
* Removed feed link from head
* Improved question page SEO #90
* Added entry-title class for titles #90
* User can create tag
* Fixed: Form validation when multibyte letters
* Improved login and signup
* updated like_escape to esc_like
* Added Crayon Syntax Highlighter computability
* Added page title
* Fixed: language loading
* Improved comment form
* Added ap_ prefix in capability
* Style improvements
* Cannot make comment on posts outside Anspress #81
* Disabled tinymce in wp_editor
* Improved question page
* Fixed: favorite button count when not logged in
* Fixed when anspress is in child page
* Set default label for new questions #51
* Improved installation page
* Order of anspress rewrite rules
* Added rewrite rule checking and flushing
* Show last activity in question #47
* Added history

= 1.0 PR9 =

* Added social login
* improved login form
* improved signup form
* updated like_escape to esc_like
* Added Crayon Syntax Highlighter computability
* Added page title
* Fixed: language loading
* Improved comment form
* Added ap_ prefix in capability
* Style improvements
* Cannot make comment on posts outside Anspress #81
* Disabled tinymce in wp_editor
* Improved question page
* Fixed: favorite button count when not logged in
* Fixed when anspress is in child page
* Set default label for new questions #51
* Improved installation page
* Order of anspress rewrite rules
* Added rewrite rule checking and flushing
* Show last activity in question #47
* fixed: undefined obj 

= 1.0 PR7 =

* Default badges #87
* Added badges page for user #86
* Updated badges functions
* Added avatar upload
* #41 Related questions
* Added sorting in users
* Added questions widget widgets/questions.php #37
* Added categories widget #36
* Fixed ap_get_link_to
* Does not work with /%category%/%postname% slug #9 


= 1.0 PR =
* Added auto installation page
* Improved category page
* Improved tags page
* Fixed message button
* Improved users profile 
* Basic email notification system #4 
* #50 Addon control page 
* #48 and #49, followers and following page 
* Added moderate page in admin where all post waiting for moderation can be listed
* Added flagged page where all flagged question and answer can be seen.
* Added new post_status : moderate
* Hold question for moderation
* Hold question for moderation based on user points
* Added reCAPTCHA
* Fixed some undefined issue, and improved cover size
* Add user page #46
* Remove ask form if no question found and instead add a link to ask form
* User's favorites page #33
* Added user answers page #31
* Fixed email validation in APjs, and added user question page #30
* Added blank html and htaccess for preventing directory listing 
* Realtime loading
* Added messaging system
* Added messages table
* Added user profile edit
* Added cover upload 
* Added user profile edit
* Added points events
* Added delete action in points
* Added points editing
* Improved AnsPress option Added option to disable tags Added option to
* Added tags suggestion and label options
* fixed followers query
* fixed view count system to use ap_meta table
* Updated participants to use ap_meta table
* Added delete option
* Fixed flag and flag meta box
* Added anspress meta and improved voting
* fix error on activation
* Added best answer and ajaxified comments
* Added ajax question submit and validation
* Added full ajax, validation and custom fields in answer form
* Adding Ajax for answer submission
* User template
* Added user ranks
* Improved question page layout
* added question sidebar
* Improved question sorting by question_label
* Added question label in template
* Added color field in question_label
* Moved label to new file
* Added question label
* Added participants
* Added sorting for taxonomy
* Added sorting of questions
* Added moderation page
* Added dashboard 