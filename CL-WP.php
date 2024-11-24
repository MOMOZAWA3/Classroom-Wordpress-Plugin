<?php
/*
Plugin Name: Classroom to WordPress
Description: 从 Google Classroom 获取数据并发布到 WordPress
Version: 1.3
Author: NI YUNHAO
*/

// 引入 Google API 客户端库
require_once __DIR__ . '/vendor/autoload.php';

class ClassroomToWordPress {
    private $client;
    private $service;

    public function __construct() {
        add_action('admin_menu', array($this, 'create_menu'));
        add_action('admin_post_google_classroom_auth', array($this, 'handle_auth_callback'));
        add_action('admin_post_list_students', array($this, 'list_students'));
        add_action('admin_post_get_student_work', array($this, 'get_student_work'));
        add_action('admin_post_get_all_student_grades', array($this, 'get_all_student_grades'));
        $this->initialize_google_client();
    }

    private function initialize_google_client() {
        $token_file = __DIR__ . '/token.json';
    
        if (!file_exists($token_file)) {
            // 记录 token.json 缺失状态
            add_action('admin_notices', function () {
                echo '<div class="notice notice-warning"><p><strong>Google Classroom 插件提醒：</strong>缺少 <code>token.json</code> 文件，请前往插件设置页面完成配置。</p></div>';
            });
            return; // 停止初始化，插件仍可启用
        }
    
        try {
            $this->client = new Google_Client();
            $this->client->setAuthConfig($token_file);
    
            // 添加有效的 Google Classroom API 作用域
            $this->client->addScope('https://www.googleapis.com/auth/classroom.courses');
            $this->client->addScope('https://www.googleapis.com/auth/classroom.courses.readonly');
            $this->client->addScope('https://www.googleapis.com/auth/classroom.coursework.students');
            $this->client->addScope('https://www.googleapis.com/auth/classroom.coursework.students.readonly');
            $this->client->addScope('https://www.googleapis.com/auth/classroom.profile.emails');
            $this->client->addScope('https://www.googleapis.com/auth/classroom.profile.photos');
            $this->client->addScope('https://www.googleapis.com/auth/classroom.rosters');
            $this->client->addScope('https://www.googleapis.com/auth/classroom.rosters.readonly');
            $this->client->addScope('https://www.googleapis.com/auth/classroom.coursework.me');
            $this->client->addScope('https://www.googleapis.com/auth/classroom.student-submissions.me.readonly');
    
            $this->client->setRedirectUri(admin_url('admin-post.php?action=google_classroom_auth'));
            $this->service = new Google_Service_Classroom($this->client);
        } catch (Exception $e) {
            add_action('admin_notices', function () use ($e) {
                echo '<div class="notice notice-error"><p>初始化 Google 客户端时出错：' . esc_html($e->getMessage()) . '</p></div>';
            });
        }
    }
    

    private function get_user_role() {
        try {
            $profile = $this->service->userProfiles->get('me');
            error_log('User Profile: ' . print_r($profile, true));
    
            // 检查 verifiedTeacher 字段
            if ($profile->getVerifiedTeacher()) {
                error_log('User is a VERIFIED TEACHER');
                return 'teacher';
            }
    
            // 检查课程中的教师角色
            $courses = $this->service->courses->listCourses()->getCourses();
            if (!empty($courses)) {
                foreach ($courses as $course) {
                    try {
                        $teacher = $this->service->courses_teachers->get($course->getId(), 'me');
                        if ($teacher) {
                            error_log('User identified as TEACHER in course: ' . $course->getName());
                            return 'teacher';
                        }
                    } catch (Exception $e) {
                        // 忽略不是教师的课程
                    }
                }
            }
        } catch (Exception $e) {
            error_log('Error fetching user role: ' . $e->getMessage());
        }
        error_log('User identified as STUDENT (default)');
        return 'student';
    }     
        
    public function create_menu() {
        add_menu_page(
            'Google Classroom Grades',
            'Classroom Grades to WP',
            'manage_options',
            'classroom-grades-to-wp',
            array($this, 'display_auth_button')
        );

        add_submenu_page(
            'classroom-grades-to-wp',
            '课程列表',
            '显示课程列表',
            'manage_options',
            'list-all-courses',
            array($this, 'list_all_courses')
        );
    }

    public function display_auth_button() {
        $token_file = __DIR__ . '/token.json';
        $auth_completed = get_option('google_classroom_auth_completed', false); // 获取授权完成标志
    
        // 处理表单提交
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token_json'])) {
            $token_content = trim(stripslashes($_POST['token_json'])); // 去掉多余反斜杠和首尾空格
            file_put_contents($token_file, $token_content); // 保存内容
            update_option('google_classroom_auth_completed', false); // 每次更新后清除授权完成标志
            echo '<div class="notice notice-success"><p>token.json 文件已成功保存，请重新授权。</p></div>';
        }
    
        echo '<h2>Classroom to WordPress</h2>';
    
        // 检查是否存在 token.json 文件
        if (!file_exists($token_file)) {
            echo '<p>未找到 <code>token.json</code> 文件，请在下方输入内容并保存。</p>';
            echo '<form method="POST">';
            echo '<textarea name="token_json" rows="10" cols="50" style="width:100%;"></textarea><br>';
            echo '<button type="submit" class="button button-primary">保存 token.json</button>';
            echo '</form>';
            return; // 停止后续显示，等待用户提供文件
        }
    
        // 授权完成时提供重新提交按钮
        if ($auth_completed) {
            echo '<p><strong>token.json 已保存且授权完成。</strong></p>';
            echo '<form method="POST" action="">';
            echo '<button type="submit" name="show_update_form" class="button button-primary">重新提交新的 token.json</button>';
            echo '</form>';
            
            // 检查是否需要显示更新表单
            if (isset($_POST['show_update_form'])) {
                echo '<p>请在下方输入新的 token.json 内容并提交：</p>';
                echo '<form method="POST">';
                echo '<textarea name="token_json" rows="10" cols="50" style="width:100%;"></textarea><br>';
                echo '<button type="submit" class="button button-primary">保存 token.json</button>';
                echo '</form>';
                return; // 停止后续显示
            }
        } else {
            // 未完成授权时显示当前 token.json 和更新表单
            $current_content = file_get_contents($token_file);
            echo '<p><strong>当前 token.json 内容：</strong></p>';
            echo '<pre style="background: #f9f9f9; padding: 10px; border: 1px solid #ddd;">' . esc_html($current_content) . '</pre>';
            echo '<p>如果需要更新，请输入新的内容并保存：</p>';
            echo '<form method="POST">';
            echo '<textarea name="token_json" rows="10" cols="50" style="width:100%;">' . esc_textarea($current_content) . '</textarea><br>';
            echo '<button type="submit" class="button button-primary">更新 token.json</button>';
            echo '</form>';
        }
    
        // 显示 Google Classroom 授权按钮
        $access_token = get_option('google_classroom_access_token');
        $auth_url = $this->client->createAuthUrl();
        echo '<a href="' . esc_url($auth_url) . '" class="button">重新授权 Google Classroom</a>';
    
        // 处理授权状态
        if ($access_token) {
            $this->client->setAccessToken($access_token);
    
            if (!$this->client->isAccessTokenExpired()) {
                echo '<p>授权成功，可以获取课程数据。</p>';
                update_option('google_classroom_auth_completed', true); // 设置授权完成标志
            } else {
                echo '<p>令牌已过期，请先重新授权。</p>';
                update_option('google_classroom_auth_completed', false); // 清除授权完成标志
            }
        } else {
            echo '<p>请先重新授权以激活数据获取功能。</p>';
            update_option('google_classroom_auth_completed', false); // 清除授权完成标志
        }
    }     
    
    public function handle_auth_callback() {
        if (isset($_GET['code'])) {
            $this->client->authenticate($_GET['code']);
            $access_token = $this->client->getAccessToken();
            update_option('google_classroom_access_token', $access_token);
        }
        wp_redirect(admin_url('admin.php?page=classroom-grades-to-wp'));
        exit;
    }
    
    public function list_all_courses() {
    $access_token = get_option('google_classroom_access_token');
    if (!$access_token) {
        wp_redirect(admin_url('admin.php?page=classroom-grades-to-wp&auth=1'));
        exit;
    }

    $this->client->setAccessToken($access_token);

    if ($this->client->isAccessTokenExpired()) {
        $refresh_token = $this->client->getRefreshToken();
        $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
        update_option('google_classroom_access_token', $this->client->getAccessToken());
    }

    try {
        $role = $this->get_user_role(); // 获取用户角色
        $courses = $this->service->courses->listCourses()->getCourses();
        if (empty($courses)) {
            echo '没有找到任何课程数据。';
            return;
        }

        echo '<h2>可访问的课程列表</h2>';
        foreach ($courses as $course) {
            $course_id = esc_attr($course->getId());
            $course_name = esc_html($course->getName());

            echo '<div>';
            echo '<h3>' . $course_name . '（课程ID: ' . $course_id . '）</h3>';
            echo '<a href="' . esc_url(admin_url("admin-post.php?action=list_students&course_id=$course_id")) . '" class="button">显示学生列表</a>';

            // 如果是教师，显示“获取所有学生成绩”按钮
            if ($role === 'teacher') {
                echo '<a href="' . esc_url(admin_url("admin-post.php?action=get_all_student_grades&course_id=$course_id")) . '" class="button">获取所有学生成绩</a>';
            }

            echo '</div><hr>';
        }
    } catch (Exception $e) {
        echo '获取课程列表时出错: ', esc_html($e->getMessage());
    }
}

    public function list_students() {
        if (!isset($_GET['course_id'])) {
            echo '课程ID未指定。';
            return;
        }

        $course_id = sanitize_text_field($_GET['course_id']);
        $access_token = get_option('google_classroom_access_token');
        if (!$access_token) {
            wp_redirect(admin_url('admin.php?page=classroom-grades-to-wp&auth=1'));
            exit;
        }

        $this->client->setAccessToken($access_token);

        if ($this->client->isAccessTokenExpired()) {
            $refresh_token = $this->client->getRefreshToken();
            $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
            update_option('google_classroom_access_token', $this->client->getAccessToken());
        }

        try {
            $students = $this->service->courses_students->listCoursesStudents($course_id)->getStudents();
            if (empty($students)) {
                echo '该课程没有学生数据。';
                return;
            }

            echo '<h2>学生列表</h2>';
            echo '<ul>';
            foreach ($students as $student) {
                $student_id = esc_attr($student->getUserId());
                $student_name = esc_html($student->getProfile()->getName()->getFullName());
                $student_email = esc_html($student->getProfile()->getEmailAddress());

                echo '<li>';
                echo $student_name . ' (' . $student_email . ') ';
                echo '<a href="' . esc_url(admin_url("admin-post.php?action=get_student_work&course_id=$course_id&student_id=$student_id")) . '" class="button">获取作业和成绩</a>';
                echo '</li>';
            }
            echo '</ul>';
        } catch (Exception $e) {
            echo '获取学生列表时出错: ', esc_html($e->getMessage());
        }
    }

    private function generate_grade_table($students, $courseworks, $submissions_data) {
        $html = '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">';
        $html .= '<tr><th>学生姓名</th>';
    
        // 添加作业名称为列标题
        foreach ($courseworks as $work) {
            $html .= '<th>' . esc_html($work->getTitle()) . '</th>';
        }
        $html .= '</tr>';
    
        // 填充学生数据
        foreach ($students as $student) {
            $student_name = esc_html($student->getProfile()->getName()->getFullName());
            $html .= '<tr>';
            $html .= '<td>' . $student_name . '</td>';
    
            foreach ($courseworks as $work) {
                $work_id = $work->getId();
                $student_id = $student->getUserId();
                $grade = isset($submissions_data[$student_id][$work_id]) ? $submissions_data[$student_id][$work_id] : '未提交';
    
                $html .= '<td>' . $grade . '</td>';
            }
            $html .= '</tr>';
        }
    
        $html .= '</table>';
        return $html;
    }
    
    public function get_all_student_grades() {
        if (!isset($_GET['course_id'])) {
            echo '课程ID未指定。';
            return;
        }
    
        $course_id = sanitize_text_field($_GET['course_id']);
        $access_token = get_option('google_classroom_access_token');
        if (!$access_token) {
            wp_redirect(admin_url('admin.php?page=classroom-grades-to-wp&auth=1'));
            exit;
        }
    
        $this->client->setAccessToken($access_token);
    
        if ($this->client->isAccessTokenExpired()) {
            $refresh_token = $this->client->getRefreshToken();
            $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
            update_option('google_classroom_access_token', $this->client->getAccessToken());
        }
    
        try {
            // 获取课程名称
            $course = $this->service->courses->get($course_id);
            $course_name = esc_html($course->getName());
    
            // 获取学生列表
            $students = $this->service->courses_students->listCoursesStudents($course_id)->getStudents();
            if (empty($students)) {
                echo '该课程没有学生数据。';
                return;
            }
    
            // 获取课程作业
            $courseworks = $this->service->courses_courseWork->listCoursesCourseWork($course_id)->getCourseWork();
            if (empty($courseworks)) {
                echo '该课程没有作业。';
                return;
            }
    
            // 获取每个学生每个作业的提交数据
            $submissions_data = [];
            foreach ($courseworks as $work) {
                $work_id = $work->getId();
                $submissions = $this->service->courses_courseWork_studentSubmissions
                    ->listCoursesCourseWorkStudentSubmissions($course_id, $work_id)
                    ->getStudentSubmissions();
    
                foreach ($submissions as $submission) {
                    $student_id = $submission->getUserId();
                    $grade = $submission->getAssignedGrade() ?? '未提交';
                    $submissions_data[$student_id][$work_id] = $grade;
                }
            }
    
            // 生成表格 HTML
            $grade_table = $this->generate_grade_table($students, $courseworks, $submissions_data);
    
            // 创建 WordPress 文章
            $this->create_wp_post("$course_name - 学生成绩表", '学生成绩', $grade_table);
    
            echo '<p>学生成绩表已成功发布为文章。</p>';
            wp_redirect(admin_url('edit.php'));
            exit;
    
        } catch (Exception $e) {
            echo '获取学生成绩时出错: ', esc_html($e->getMessage());
        }
    }    
    
    private function get_student_grades($course_id, $student_id) {
        $grades = '';
        try {
            $courseworks = $this->service->courses_courseWork->listCoursesCourseWork($course_id)->getCourseWork();
            foreach ($courseworks as $work) {
                $work_id = $work->getId();
                $submissions = $this->service->courses_courseWork_studentSubmissions
                    ->listCoursesCourseWorkStudentSubmissions($course_id, $work_id, ['userId' => $student_id])
                    ->getStudentSubmissions();
    
                foreach ($submissions as $submission) {
                    $grade = $submission->getAssignedGrade() ?? '未评分';
                    $grades .= $work->getTitle() . ": $grade<br>";
                }
            }
        } catch (Exception $e) {
            $grades = '无法获取成绩';
        }
        return $grades;
    }    
    
    public function get_student_work() {
        if (!isset($_GET['course_id']) || !isset($_GET['student_id'])) {
            echo '课程ID或学生ID未指定。';
            return;
        }
    
        $course_id = sanitize_text_field($_GET['course_id']);
        $student_id = sanitize_text_field($_GET['student_id']);
        $access_token = get_option('google_classroom_access_token');
        if (!$access_token) {
            wp_redirect(admin_url('admin.php?page=classroom-grades-to-wp&auth=1'));
            exit;
        }
    
        $this->client->setAccessToken($access_token);
    
        if ($this->client->isAccessTokenExpired()) {
            $refresh_token = $this->client->getRefreshToken();
            $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
            update_option('google_classroom_access_token', $this->client->getAccessToken());
        }
    
        try {
            $courseworks = $this->service->courses_courseWork->listCoursesCourseWork($course_id)->getCourseWork();
            if (empty($courseworks)) {
                echo '该课程没有作业数据。';
                return;
            }
    
            $student_work = "<h3>学生作业和成绩</h3><table border='1' cellpadding='5' cellspacing='0'>
            <tr><th>作业标题</th><th>作业描述</th><th>提交状态</th><th>成绩</th><th>学生附件</th><th>教师附件</th></tr>";
    
            foreach ($courseworks as $work) {
                $work_id = $work->getId();
                $work_title = esc_html($work->getTitle());
                $work_description = nl2br(esc_html($work->getDescription() ?? '无描述'));
    
                // 提取教师发布的附件
                $teacher_attachments = $this->get_teacher_attachments($work->materials);
    
                try {
                    $submissions = $this->service->courses_courseWork_studentSubmissions
                        ->listCoursesCourseWorkStudentSubmissions($course_id, $work_id, ['userId' => $student_id])
                        ->getStudentSubmissions();
    
                    if (empty($submissions)) {
                        $student_work .= "<tr><td>$work_title</td><td>$work_description</td><td>未提交</td><td>无成绩</td><td>无附件</td><td>$teacher_attachments</td></tr>";
                        continue;
                    }
    
                    foreach ($submissions as $submission) {
                        // 检查提交状态
                        $state = $submission->getState();
                        $status = ($state === 'TURNED_IN') ? '已提交' : '未提交';
                    
                        $grade = $submission->getAssignedGrade() ?? '无成绩';
                    
                        // 提取学生提交的附件
                        $attachments = $submission->assignmentSubmission->attachments ?? []; // 修正解析方法
                        $attachment_links = $this->get_student_attachments($attachments);
                    
                        $student_work .= "<tr><td>$work_title</td><td>$work_description</td><td>$status</td><td>$grade</td><td>$attachment_links</td><td>$teacher_attachments</td></tr>";
                    }                                     
                } catch (Exception $e) {
                    $student_work .= "<tr><td>$work_title</td><td>$work_description</td><td>错误</td><td>无法获取提交</td><td>无附件</td><td>$teacher_attachments</td></tr>";
                    error_log("Error fetching submissions for work_id: $work_id, student_id: $student_id - " . $e->getMessage());
                }
            }
    
            $student_work .= "</table>";
    
            try {
                $student = $this->service->courses_students->get($course_id, $student_id);
                $student_name = esc_html($student->getProfile()->getName()->getFullName());
            } catch (Exception $e) {
                $student_name = "未知学生 ($student_id)";
                error_log("Error fetching student details for student_id: $student_id - " . $e->getMessage());
            }
            
            try {
                $course = $this->service->courses->get($course_id);
                $course_name = esc_html($course->getName());
            } catch (Exception $e) {
                $course_name = "未知课程 ($course_id)";
                error_log("Error fetching course details for course_id: $course_id - " . $e->getMessage());
            }
            
            $this->create_wp_post(
                "$course_name - $student_name",
                '学生成绩信息',
                $student_work
            );            
    
            echo '<p>学生作业和成绩已成功发布为文章。</p>';
            wp_redirect(admin_url('edit.php'));
            exit;
    
        } catch (Exception $e) {
            echo '获取学生作业和成绩时出错: ', esc_html($e->getMessage());
            error_log("Error fetching student work for course_id: $course_id, student_id: $student_id - " . $e->getMessage());
        }
    }
    
    private function get_teacher_attachments($materials) {
        $attachments = '';
        if (!empty($materials)) {
            foreach ($materials as $material) {
                // 检查是否存在 driveFile 并解析嵌套结构
                if (isset($material->driveFile) && isset($material->driveFile->driveFile)) {
                    $driveFile = $material->driveFile->driveFile;
                    $file_title = $driveFile->title ?? '无标题文件';
                    $file_url = $driveFile->alternateLink ?? '#';
                    $attachments .= "<a href='" . esc_url($file_url) . "' target='_blank'>" . esc_html($file_title) . " (Google Drive 文件)</a><br>";
                } elseif (isset($material->form)) {
                    // Google Form 附件
                    $form_title = $material->form->title ?? '无标题表单';
                    $form_url = $material->form->formUrl ?? '#';
                    $attachments .= "<a href='" . esc_url($form_url) . "' target='_blank'>" . esc_html($form_title) . " (Google 表单)</a><br>";
                } elseif (isset($material->link)) {
                    // 链接附件
                    $link_title = $material->link->title ?? '无标题链接';
                    $link_url = $material->link->url ?? '#';
                    $attachments .= "<a href='" . esc_url($link_url) . "' target='_blank'>" . esc_html($link_title) . " (链接)</a><br>";
                } elseif (isset($material->youtubeVideo)) {
                    // YouTube 视频附件
                    $video_title = $material->youtubeVideo->title ?? '无标题视频';
                    $video_url = $material->youtubeVideo->alternateLink ?? '#';
                    $attachments .= "<a href='" . esc_url($video_url) . "' target='_blank'>" . esc_html($video_title) . " (YouTube 视频)</a><br>";
                } else {
                    $attachments .= "未知附件类型<br>";
                }
            }
        } else {
            $attachments = "无附件";
        }
        return $attachments;
    }    
    
    private function get_student_attachments($attachments) {
        $attachment_links = '';
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                if (isset($attachment->driveFile)) {
                    $file_title = $attachment->driveFile->title ?? '无标题文件';
                    $file_url = $attachment->driveFile->alternateLink ?? '#';
                    $attachment_links .= "<a href='" . esc_url($file_url) . "' target='_blank'>" . esc_html($file_title) . " (Google Drive 文件)</a><br>";
                } elseif (isset($attachment->link)) {
                    $link_title = $attachment->link->title ?? '无标题链接';
                    $link_url = $attachment->link->url ?? '#';
                    $attachment_links .= "<a href='" . esc_url($link_url) . "' target='_blank'>" . esc_html($link_title) . " (链接)</a><br>";
                } elseif (isset($attachment->youTubeVideo)) {
                    $video_title = $attachment->youTubeVideo->title ?? '无标题视频';
                    $video_url = $attachment->youTubeVideo->alternateLink ?? '#';
                    $attachment_links .= "<a href='" . esc_url($video_url) . "' target='_blank'>" . esc_html($video_title) . " (YouTube 视频)</a><br>";
                } else {
                    $attachment_links .= "未知附件类型<br>";
                }
            }
        } else {
            $attachment_links = "无附件";
        }
        return $attachment_links;
    }    
    
    private function create_wp_post($title, $category_name, $content = '') {
        if (post_exists($title)) return;

        $category_id = get_cat_ID($category_name);
        if (!$category_id) {
            $category_id = wp_create_category($category_name);
        }

        $post_data = array(
            'post_title'   => wp_strip_all_tags($title),
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'post',
            'post_category' => array($category_id)
        );

        $post_id = wp_insert_post($post_data);
        if ($post_id) {
            echo '帖子已成功创建，ID为: ' . $post_id . '<br>';
        } else {
            echo '帖子创建失败。<br>';
        }
    }
}

new ClassroomToWordPress();
