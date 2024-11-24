# Classroom to WordPress

**贡献者：** NI YUNHAO
**捐赠链接：** [https://21te495.edu2web.com/](https://21te495.edu2web.com/)  
**标签：** Google Classroom，学生成绩，作业管理，教育  
**最低要求：** WordPress 5.0  
**测试通过版本：** WordPress 6.3  
**最低 PHP 版本要求：** 7.4  
**稳定版本：** 1.3  
**许可证：** GPLv2 或更高版本  
**许可证链接：** [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)  

Classroom to WordPress 插件将 Google Classroom 与 WordPress 无缝连接，可以获取学生数据、作业和成绩，并将其发布到 WordPress。

---

## 插件简介

**Classroom to WordPress** 插件可以帮助教师和管理员整合 Google Classroom 数据至 WordPress，以提升教育工作流程。主要功能包括：

- 获取并列出 Google Classroom 中的所有课程。
- 查看课程中学生的详细名单。
- 获取单个学生或整班学生的作业和成绩。
- 自动将课程数据、作业或成绩发布为 WordPress 文章。

这款插件特别适合希望在 WordPress 网站上展示和管理 Google Classroom 数据的教育工作者和教育机构。

---

## 功能特色

- **Google Classroom API 集成**：轻松完成身份验证并获取 Google Classroom 数据。
- **课程管理**：列出所有课程并查看详情。
- **学生管理**：展示学生名单及其电子邮件和个人资料。
- **作业与成绩获取**：获取学生提交的作业及其成绩。
- **文章自动创建**：将学生数据直接发布为 WordPress 文章。
- **角色识别**：根据用户角色（教师或学生）调整功能权限。

---

## 安装步骤

1. 下载插件并上传到 `/wp-content/plugins/` 目录。
2. 在 WordPress 管理后台的插件页面启用插件。
3. 通过后台菜单 **“Classroom Grades to WP”** 进入插件设置页面进行配置。

---

## 配置步骤

1. 在 [Google API 控制台](https://console.cloud.google.com/) 中设置 OAuth 2.0 客户端后，获取 `token.json` 文件。
2. 在插件设置页面上传 `token.json` 文件，或者直接将文件内容粘贴到输入框中。
3. 点击 **“授权 Google Classroom”** 按钮授予必要权限。
4. 授权完成后，即可通过插件界面访问课程数据、作业和成绩。

---

## 常见问题

### 1. 我如何获取 `token.json` 文件？
在 [Google API 控制台](https://console.cloud.google.com/) 设置 OAuth 2.0 客户端后，您可以下载 `token.json` 文件。

### 2. 如果访问令牌过期怎么办？
插件会自动刷新过期的令牌。如果刷新失败，您需要重新授权。

### 3. 学生可以使用这个插件吗？
该插件主要为教师和管理员设计。学生仅在获得访问权限时可查看特定数据。

---

## 插件截图

1. **插件设置页面**：展示插件设置和选项概览。
2. **课程列表**：显示从 Google Classroom 获取的课程。
3. **学生成绩表**：展示作业和成绩的详细表格。
4. **WordPress 文章展示**：插件自动生成的学生成绩 WordPress 文章示例。

---

## 更新日志

### 1.3
- 新增支持显示作业附件功能。
- 改进了 Google API 响应的错误处理。
- 增强用户角色检测和权限管理。

### 1.2
- 添加自动创建学生成绩和数据的 WordPress 文章功能。
- 优化 `token.json` 管理界面。

### 1.1
- 发布首个版本，提供 Google Classroom 的基本集成功能。

---

## 升级注意事项

升级前请确保 `token.json` 文件已正确配置，以避免功能中断。

---

## 许可证

本插件遵循 GNU 通用公共许可证 (GPLv2 或更高版本)。  
详情请参考 [GPLv2 许可证](http://www.gnu.org/licenses/gpl-2.0.html)。

---

## 技术支持

如需技术支持或有任何问题，请联系：  
**你的名字**  
**邮箱：** your-email@example.com
