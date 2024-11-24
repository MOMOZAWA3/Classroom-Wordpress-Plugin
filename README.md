# Classroom to WordPress

**贡献者：** NI YUNHAO  
**捐赠链接：** [https://21te495.edu2web.com/](https://21te495.edu2web.com/)   
**标签：** Google Classroom，学生成绩，教育  
**最低要求：** WordPress 5.0  
**测试通过版本：** WordPress 6.3  
**最低 PHP 版本要求：** 7.4  
**稳定版本：** 1.3  
**许可证：** GPLv2 或更高版本  
**许可证链接：** [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)  

Classroom to WordPress 插件将 Google Classroom 与 WordPress 无缝连接，可以获取学生数据、作业和成绩，并将其发布到 WordPress。

## 插件简介

**Classroom to WordPress** 插件可以帮助教师和学生整合 Google Classroom 数据至 WordPress，以实现自己的数据自己管理。主要功能包括：

### 学生

- 获取并列出 Google Classroom 中的所有课程。

- 获取课程中自己的作业和成绩，自动将课程数据、作业或成绩发布为 WordPress 文章。

### 教师

- 获取并列出 Google Classroom 中的所有课程。

- 查看课程中学生的详细名单。

- 获取单个学生或整班学生的作业和成绩，自动将课程数据、作业或成绩发布为 WordPress 文章。

这款插件特别适合希望能自己的学习数据自己管理的人，把学习数据数据去中心化。

## 功能特色

- **Google Classroom API 集成**：轻松完成身份验证并获取 Google Classroom 数据。

- **课程管理**：列出所有课程并查看详情。

- **学生管理**：展示学生名单及其电子邮件和个人资料。

- **作业与成绩获取**：获取学生提交的作业及其成绩。

- **文章自动创建**：将学生数据直接发布为 WordPress 文章。

- **角色识别**：根据用户角色（教师或学生）调整功能权限。

## 安装步骤

1.点击页面右侧的绿色按钮 “Code”。  

2.在下拉菜单中选择 “Download ZIP” 下载插件的压缩文件。 

3.登录到您的 WordPress 管理后台。 

4.在左侧菜单中点击 插件 > 安装插件。

5.在页面左上角点击 “上传插件” 按钮。 

6.点击 “选择文件”，选择刚刚从 GitHub 下载的 ZIP 文件，然后点击 “现在安装”。    

7.安装完成后，点击 “启用插件” 按钮。

8.插件安装成功后，您可以在左侧菜单找到 “Classroom Grades to WP”，并开始配置和使用插件。  

## 配置步骤

1. 在 [Google API 控制台](https://console.cloud.google.com/) 中设置 OAuth 2.0 客户端后，获取Json文件。

2. 打开Json文件并复制文件内容。

3. 在插件设置页面将复制内容粘贴到输入框中，然后点击 “保存token.json”。

5. 点击 **“授权 Google Classroom”** 按钮授予必要权限。

6. 授权完成后，即可通过插件界面访问课程数据、作业和成绩。

## 如何获取 `json` 文件？

### 登录 Google Cloud Console

1. 打开 [Google Cloud Console](https://console.cloud.google.com/) 。

2. 使用您的 Google 账号登录。
 
![登录页面](img/Classroomapi1.png)

### 创建新项目

1. 在顶部导航栏点击 “选择项目”。

![选择项目](img/Classroomapi2.png)

2. 点击 “新建项目”。

![新建项目](img/Classroomapi3.png)

3. 输入项目名称（如 Classroom to WordPress），选择适当的组织和位置（如 ditu.jp），点击 “创建”。

![创建](img/Classroomapi4.png)

### 启用 Google Classroom API

1.在项目创建后，点击“通知内容”。

![通知内容](img/Classroomapi5.png)

2.点击左侧产品中的 “API 和服务”。

![API 和服务](img/Classroomapi6.png)

3.点击左侧菜单中的 “库”。

![库](img/Classroomapi7.png)

4.在搜索框中输入 “Google Classroom API”，按下“回车”。

![输入并回车](img/Classroomapi8.png)

5.选择 “Google Classroom API”。

![选择](img/Classroomapi9.png)

6.点击 “启用”。

![启用](img/Classroomapi10.png)

7.等待 API 启用完成，页面会自动跳转到 API 的详细信息页面。

![跳转界面](img/Classroomapi11.png)

### 创建 OAuth 权限请求页面

1.点击左侧菜单中的“OAuth 权限请求页面”。

![OAuth 权限请求页面](img/Classroomapi12.png)

2.选择内部并点击“创建”。

![内部创建](img/Classroomapi13.png)

3.输入应用名称（如 Classroom to WordPress），选择用户支持邮件（选择当前登录账号邮件）。

![输入并选择](img/Classroomapi14.png)

4.点击 “添加网域” 输入以获授权的网域（自己WordPress域，如 21TE495.edu2web.com就是edu2web.com）和电子邮件地址（任意）并点击 “保存并继续”。

![输入](img/Classroomapi15.png)

5.不用做更改直接点击 “保存并继续”。

![保存并继续](img/Classroomapi16.png)

6.点击 “返回信息中心”。 

![返回信息中心](img/Classroomapi17.png)

7.完成OAuth 权限请求页面的创建。

![完成](img/Classroomapi18.png)

### 创建 OAuth 2.0 客户端 ID 凭据

1.点击左侧菜单中的 “凭据”。

![凭据](img/Classroomapi19.png)

2.点击上方 “创建凭据”。

![创建凭据](img/Classroomapi20.png)

3.点击 “OAuth 客户端 ID”。

![OAuth 客户端 ID](img/Classroomapi21.png)

4.选择应用类型 “Web应用”。

![Web应用](img/Classroomapi22.png)

5.输入名称（如 Classroom to WordPress），添加已获授权的JavaScript（自己WordPress网站）和已获授权的重定向URL（自己WordPress网站 + /wp-admin/admin-post.php?action=google_classroom_auth）并点击 “创建”。

![URL](img/Classroomapi23.png)

### 下载 JSON 文件

1.创建完成后，系统会生成 客户端 ID 和 客户端密钥。

2.点击 “下载JSON” 按钮，将 JSON 文件下载到本地。

![下载JSON](img/Classroomapi24.png)

3.将此 JSON 文件保存好，以便在 WordPress 插件中配置。

## 插件截图

1. **插件设置页面**：展示插件设置和选项概览。

2. **课程列表**：显示从 Google Classroom 获取的课程。

3. **学生成绩表**：展示作业和成绩的详细表格。

4. **WordPress 文章展示**：插件自动生成的学生成绩 WordPress 文章示例。

## 更新日志

### 1.3
- 新增支持显示作业附件和教师附件功能。

- 改进了 Google API 响应的错误处理。

- 增强用户角色（教师或学生）检测和权限管理。

### 1.2
- 添加自动创建学生成绩和数据的 WordPress 文章功能。

- 优化 `token.json` 管理界面。

### 1.1
- 发布首个版本，提供 Google Classroom 的基本认证功能。

## 升级注意事项

升级前请确保 `token.json` 文件已正确配置，以避免功能中断。

## 许可证

本插件遵循 GNU 通用公共许可证 (GPLv2 或更高版本)。  

详情请参考 [GPLv2 许可证](http://www.gnu.org/licenses/gpl-2.0.html)。

## 技术支持

**如需技术支持或有任何问题，请联系：** NI YUNHAO  

**邮箱：** 21TE495@ditu.jp
