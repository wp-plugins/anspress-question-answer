=== AnsPress - Question and answer plugin ===
Contributors: nerdaryan
Donate link: https://www.paypal.com/cgi-bin/webscr?business=rah12@live.com&cmd=_xclick&item_name=Donation%20to%20AnsPress%20development
Tags: question, answer, q&a, forum, profile, stackoverflow, quora, buddypress
Requires at least: 4.1.1
Tested up to: 4.1.1
Stable tag: 2.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

AnsPress - Best question and answer plugin for WordPress. Made with developers in mind, highly customizable

== Description ==
Demo & support forum: http://wp3.in/questions/


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
  * reCaptcha
  * User level : Participant, Editor, Moderator
  * Email notification
  * User can add questions to his favorite
  * User can edit profile
  * User can upload cover photo
  * Friends and followers system
  * User points system (reputation)


= Developers =

Project site: [WP3](http://wp3.in)
GitHub repo: [Git AnsPress](https://github.com/wp3/anspress/)
Twitter: [@wp3in](http://twitter.com/wp3in)
GooglePlus: [@wp3](https://plus.google.com/+rahularyannerdaryan?rel=author)

= Help & Support =
For fast help and support, please post on our forum http://wp3.in/questions/


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

= 2.0.4 =

* [fix] AnsPress options
* [fix] hide any existing loding animation
* [fix] Wrong redirect after deleting question
* [added] ap_get_all_reputation
* Updated admin menu position
* Loading animation for select button
* [added] loading animation and improved notification


= 2.0.3 =

* Check empty comment before posting
* refresh question suggestion on backspace
* Include previous version point count
* Remove unused options


= 2.0.2 =

* Rename old tags
* Added category widget
* Added widget position in base page
* fix button sizes
* Added two options; - Disable OP to answer their question (not applied for admin) - Disallow multiple answer per question 9not applied for admin) [fix] - Show wrong message when user already answered
* fixed voting button on answer
* Added requirement checker
* [fix] undefined index
* Removed unused functions
* Load buddypress file on bp_loaded
* Added notification for answers and comments
* fixed some major bugs
* added doc comments for anspress_sc
* Added parameters for [anspress] shortcode
* [fix] sorting on home page
* [fix] run flush_rules only ap_opt('ap_flush') == 'true'
* Best practice issues
* Don't let reputation to negative
* flush apmeta when related post gets deleted
* [fix] delete flag meta on post trash
* fixed bug in reputation.php
* Improved question page style
* Improved list style
* added reputation page in buddypress
* Disable reputation for anonymous
* Check metions in answers
* Check mention in questions
* Added reputation and fixed meta delete
* replace action link
* [new] add question and answer to buddypress activity stream
* [fix] pagination in buddypress
* Added answers in buddypress
* Added questions tab in buddypress
* Detect buddypress and change user link to BP profile
* removed .updated class
* Added captcha in Answer form
* Added reCaptcha
* Styled editor
* Added questions list widget
* hook option on admin_init
* #262 Disable quicktags in questions
* [fix] related questions widget
* Improved subscribe button
* Exclude best answer from main Answers_Query
* [fix] Answer sorting
* Added author credit
* Clear editor after posting answer
* Added option to disable voting
* Update comment count on new comment
* Auto update comment count
* Added publish class in time
* Fix answer count in answers list
* [fix] ajax loading comments in answers
* [fix] ajax loading after editing comment
* Posts actions as dropdown menu
* Checkbox inline description
* [fix] allow anonymous for answer
* Removed editor toggle and moved media button
* Added option showing title in question page
* Fixed ajax loading while answer is first.
* Added .published class in time
* Fixed user link
* Lost MEDIA library option from admins menu
* Updated style
* declare the visibility for methods
* Added uninstall hook
* Removed unused files
* [fix] questions page paging rewrite
* Missing argument 2 for AnsPress_Theme::the_title()
* Improved admin style and added option for locking delete action
* Show CPT menu for delete_pages
* Show AnsPress menu if user have manage_options
* Load shortcode on init
* Held for moderation
* Fixed some bugs and added grunt
* [fix] Subscribe button
* Fixed conflicts with profile
* [fix] use question title in answer title
* Made it compatible with profile plugin
* [fix] Rewrite rule when base page is child page
* Disable comments option added
* [fix] Question detail is appearing for all posts
* [fix] Search page
* Added anspress menu
* [fix] form get cleared even if there is error
* [fix] Toggle private post
* [fix] Language files were not loading
* Added option for switching text editor as default
* [fix] TwentyFifteen compatibility
* Show this label "No questions match your search criteria" when nothing found in search
* Improved question list
* Improved dashboard
* Added question select
* Improved flagging system
* Disallow answer for closed question
* Add private question & moderate sticker in question
* Enable question sidebar only if there is widget in it
* Flag button fixed
* Hide comments be default
* Added related questions widget
* Added question stats widget
* Added participants and subscribe widget
* Improved question and list page style
* Added action and filter for search
* Added search functionality
* Removed user.php and added required functions to functions.php
* Enable user link only if user extension installed
* Added extensions listing
* [fix] replaced icon- class to apicon-
* Check array before output menu
* [fix] Editing own comment only possible after refresh
* [removed] ap-site.js
* [new] Dynamic option group and fields
* Removed main.php
* Removed messages.php
* [fix] Participants caps were empty
* [fix] After deleting a question, troublesome redirection
* Improved form validation
* Added vote and view meta in list
* [fix] show register or login message for non-logged in
* [fix] Annonymous is unchecked in the backend but user can still ask question
* [fix] link of ask question button when there is no question
* [fixed] Answer permalink
* [fixed] Participant is not being inserted to DB
* [fixed] After comment actions are not working
* [fixed] Entering space in ask question title field show all questions
* [fixed] User page ap_meta_array_map function missing
* [fix]When AnsPress is set to home page then sorting does not work
* Rename sort query arg to ap_sort
* [fixed] unsolved question listing
* [fixed] Answer count update on ajax request
* [fixed] Wrong answer count
* [fixed] delete button
* [fixed] Select best answer
* [fixed] JS and ajax request
* [fixed] Answer form validation
* [fixed] Ajax answer submission
* [removed] Installation screen and cleaned up codes.

= 1.4.3 =

* Made compatible with WordPress 4.1.1
