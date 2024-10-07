Event Manager

Contributors: mrkohan
Tags: events, custom post type, event management, RSVP, WordPress REST API
Requires at least: 5.0
Tested up to: 6.6.2
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Event Manager is a comprehensive plugin that adds an "Event" custom post type to your WordPress site, allowing you to create, manage, and display events effortlessly. With features like custom taxonomies, admin enhancements, front-end display, search and filtering, user notifications, RSVP functionality, REST API integration, security measures, performance optimizations, and unit tests, Event Manager is the ideal solution for event management on your website.

Features
Custom Post Type for Events: Easily create and manage events with a dedicated post type.
Custom Taxonomies: Organize events with the "Event Type" taxonomy.
Admin Enhancements: Customize the admin list view with additional columns and sorting options.
Front-End Display: Templates for single events and archives, plus shortcodes for listing events and displaying a filter form.
Search and Filtering: Allow users to search and filter events by type and date range.
User Notifications: Automatically send email notifications to users when events are published or updated.
RSVP Functionality: Enable users to RSVP to events, recording their attendance and sending confirmation emails.
REST API Integration: Expose event data via the WordPress REST API for external applications.
Security Measures: Implement nonce checks, sanitize inputs, and escape outputs to ensure security.
Performance Optimizations: Optimize database queries, efficient front-end rendering, and utilize caching strategies.
Sample Data on Activation: Automatically create sample events and event types upon plugin activation for testing purposes.
Unit Tests: Comprehensive unit tests covering core functionalities and edge cases.
Installation
Upload the Plugin:

Upload the event-manager folder to the /wp-content/plugins/ directory.
Activate the Plugin:

Navigate to the Plugins page in the WordPress admin area and activate the Event Manager plugin.
Permalink Settings:

Go to Settings > Permalinks and click Save Changes to flush rewrite rules.
Sample Data:

Upon activation, the plugin automatically creates sample events and event types for testing purposes.
Usage
Creating Events
Navigate to Events > Add New in the WordPress admin dashboard.
Enter the event details, including title, content, event date, and location.
Assign an Event Type from the available options or create a new one.
Publish the event to make it live on your site.
Displaying Events on the Front End
Event Archive Page:

Access the archive page at http://yourwebsite.com/events/ to see all events.
Single Event Page:

Click on an event title from the archive or any listing to view its single page.
Shortcodes
Event List Shortcode
[event_list] displays a list of events.

Attributes:

type (optional): Filter events by event type slug.
date_from (optional): Show events starting from this date (YYYY-MM-DD).
date_to (optional): Show events up to this date (YYYY-MM-DD).
Example:


[event_list type="conference" date_from="2024-01-01" date_to="2024-12-31"]
Event Filter Form Shortcode
[event_filter_form] displays a form for users to filter events.

Users can select an event type and specify a date range.
RSVP Functionality
RSVP to Events
Logged-in users can RSVP to events using the form displayed on single event pages.
Upon successful RSVP, users receive a confirmation email.
Viewing RSVPs
In the admin dashboard, edit an event to view the Event RSVPs meta box, listing all users who have RSVPed.
User Notifications
New Event Notifications:

Automatically sends an email to all users when a new event is published.
Event Update Notifications:

Sends an email to all users when an event is updated.
REST API Endpoints
List Events:


GET /wp-json/event-manager/v1/events
Retrieve a Single Event:


GET /wp-json/event-manager/v1/events/{id}
Optional Query Parameters:

event_type: Filter events by event type slug.
date_from: Filter events starting from this date.
date_to: Filter events up to this date.
Templates
Overriding Templates
Copy the template files from templates/ into your theme's directory and customize them as needed.

Template files:

archive-event.php: Displays the event archive page.
single-event.php: Displays single event pages.
Frequently Asked Questions
How do I customize the event templates?
Copy the template files (archive-event.php and single-event.php) from the plugin's templates/ directory to your theme's root directory. Modify the copied files in your theme to suit your design requirements.

How can I display a list of events on a page or post?
Use the [event_list] shortcode in the content editor of the page or post where you want to display the events. You can use optional attributes to filter events by type or date range.

Can I disable the sample data creation?
By default, sample data is created upon plugin activation for testing purposes. If you prefer not to have sample data, you can remove or comment out the em_create_sample_data() function in the event-manager.php file.


Changelog
1.0.0
Initial release of Event Manager.
Added custom post type for events.
Implemented custom taxonomy for event types.
Enhanced admin interface with custom columns and sorting.
Created front-end templates for events.
Developed shortcodes for event listing and filtering.
Integrated search and filtering functionality.
Added user notifications for event publication and updates.
Implemented RSVP functionality with confirmation emails.
Exposed events via the WordPress REST API.
Ensured security through input sanitization and output escaping.
Optimized performance with caching strategies and query optimizations.
Included sample data creation upon plugin activation.
Wrote unit tests covering core functionalities and edge cases.
Upgrade Notice
1.0.0
Initial release of Event Manager. Install and activate to start managing events on your WordPress site.

License
This plugin is licensed under the GPLv2 or later. See the GNU General Public License for more details.

Note: For any questions or support, please contact mrkohan.