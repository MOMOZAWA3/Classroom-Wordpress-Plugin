=== Classroom to WordPress ===
Contributors: Your Name
Donate link: https://example.com/
Tags: google classroom, student grades, assignment management, education
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.4
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Classroom to WordPress is a plugin that connects Google Classroom with WordPress, allowing you to fetch student data, assignments, grades, and more.

== Description ==
The **Classroom to WordPress** plugin integrates Google Classroom with WordPress to enhance educational workflows. Teachers and administrators can:

- Fetch and list courses from Google Classroom.
- View detailed lists of enrolled students in a course.
- Fetch assignments and grades for individual students or an entire class.
- Automatically create WordPress posts containing course data, assignments, or grades.

This plugin is ideal for educators and institutions seeking to manage and display Google Classroom data on WordPress sites.

== Features ==
- **Google Classroom API Integration**: Authenticate and fetch Classroom data easily.
- **Course Management**: List and view details for all courses.
- **Student Management**: Display student rosters with associated emails and profiles.
- **Assignment & Grade Retrieval**: Fetch assignment submissions and grades.
- **Post Auto-Creation**: Publish student data directly to WordPress as posts.
- **Role Detection**: Adapts features based on user role (teacher or student).

== Installation ==
1. Download and upload the plugin folder to `/wp-content/plugins/`.
2. Activate the plugin in the WordPress Admin Dashboard under Plugins.
3. Configure the plugin via the "Classroom Grades to WP" menu in the WordPress Admin Dashboard.

== Setup ==
1. Obtain your `token.json` file from Google API Console after setting up an OAuth 2.0 client.
2. Upload the `token.json` file in the plugin settings page or paste the content directly into the input field.
3. Click "Authorize Google Classroom" to grant necessary permissions.
4. Once authorized, access course data, assignments, and grades from the plugin interface.

== Frequently Asked Questions ==

= Where can I get the `token.json` file? =
You can generate the `token.json` file from the [Google API Console](https://console.cloud.google.com/).

= What if the access token expires? =
The plugin will refresh expired tokens automatically. If the refresh fails, you will need to reauthorize the plugin.

= Can students use this plugin? =
This plugin is designed for teachers and administrators. Students can view specific details only if access is granted.

== Screenshots ==
1. **Plugin Dashboard**: Overview of settings and options.
2. **Course List**: List of courses fetched from Google Classroom.
3. **Student Grades Table**: A detailed table of assignments and grades.
4. **WordPress Post Output**: Example of a WordPress post created by the plugin.

== Changelog ==

= 1.3 =
* Added support for fetching and displaying attachments for assignments.
* Improved error handling for Google API responses.
* Enhanced user role detection and permissions.

= 1.2 =
* Introduced automatic WordPress post creation for grades and student data.
* Updated token management interface for easier configuration.

= 1.1 =
* Initial release with basic Google Classroom integration.

== Upgrade Notice ==
Please ensure `token.json` is configured correctly before upgrading to avoid functionality interruptions.

== License ==
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

== Support ==
For support and inquiries, please contact Your Name at `your-email@example.com`.
