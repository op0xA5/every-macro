every-macro
=
every-macro是一个基于php，为js,css等文件提供类似C语言的宏指令功能的工具。

注意
-
与CSS语法冲突，导致#选测器被误认为是宏指令。新版本中规则已改为“#后面为未知宏指令时，输出原文”，以尽可能减少冲突。如必须用于css文件，请注意不要在#选择器中使用以下单词：

- include
- define
- undef
- ifdef
- ifndef
- if
- else
- elif
- endif
- warning
- error
- pargma
- region
- endregion

原理
-
通过HTTP服务器的URL Rewrite功能，将对需要进行处理的文件类型Rewrite到every-macro.php主程序上，通过php程序读取相应的文件并解析其中的宏指令，最后作为HTTP响应实体返回给浏览器。
对于浏览器而言基本与普通文件请求无异。

解决的问题
-
1. 随着web应用越来越复杂，代码量也越来越大。在编译语言或本地脚本中，我们可以通过模块化设计使编程更为容易，但是在web应用中，一个模块意味着一次HTTP请求。every-macro的作用就是将散乱的模块打包成一个文件。
2. 可以将一些常用的功能或样式整理为库，使用include将其引入到代码中。还可通过配置项控制代码片段的选择，对库代码进行裁剪。
3. 方便调用其他工具对代码进行处理。比如自动使用uglifyjs或yuicompressor对代码进行压缩。

不足之处
-
1. 影响错误定位，难以定位到源代码文件及行号。或许可以与浏览器调试工具协商，在输出代码中添加定位信息以改善此问题。
2. 与css语法冲突（见顶部）

应注意的问题
-
1. 脚本代码不同于编译语言，使用时要注意上下文环境，可能会由此引来意外的错误或理解困难。尤其是编写库时，尽量做好封装。
2. 注意合理的缩放，将所有文件打包为一个文件并非合理。比如将jQuery库打包到您的代码中可能不是明智之举。
3. every-macro只适合用于开发环境，除非您对您服务器的性能很有信心。同时也表明我在初期不会考虑其性能优化问题。

使用举例
-
1. 引入文件

		#include "file.xxx"

2. 文本替换

		#define  ENGLISH  英语
		单词 "ENGLISH" 将被替换为 "英语"


3. 定义标记与检测标记

		#define DEBUG
		#ifdef  DEBUG
			DEBUG标记已定义
		#endif
		#ifndef DEBUG
			DEBUG标记未定义
		#endif

4. 通过定义标记控制文件引用

		#define DEBUG
		#ifdef  DEBUG
			#include "debug.js"
		#endif

5. 使用if检测定义项，并使用error给出错误提示

		#define  PARAMETER  1
		#if  PARAMETER > 0 && PARAMETER < 3
		alert(PARAMETER);
		#else
			#error PARAMETER值不被接受
		#endif

6. 使用region控制输出

		#region _REM_
			该区域中的内容不会被处理，仅作为注释
		#endregion

		#region _PROTECTED_
			该区域中的内容：
			1. 不参与外部程序处理。即不会被代码压缩工具等处理。
			2. 最终输出时被移动到文件头部。
			可用于书写文件版权等信息。
		#endregion

		#region _MACRO_
			该区域中的内存只进行宏处理，不输出文本
			#define  TEST  test ok
		#endregion

		TEST

7. 使用外部程序进行处理（例如使用uglifyjs压缩js代码）

		var text = "hello world";
		alert(text);
		
		//需要您的电脑安装相关软件
		//代码仅做示例，不保证能正常执行
		//如正常执行，您将看到压缩后的代码
		#pragma exec_as_output uglifyjs %s -mt

8. 导出输出内容至文件

		//在例子7的基础上
		#pragma dump dump_file.js
		//在该文件所在目录中将产生dump_file.js文件
		//文件内容与输出内容一致

9. 通过Querystring引入define项目

		#ifdef TEST
		test 值等于 TEST
		#else
		未定义TEST
		#endif
		
		//访问本文件时可尝试加上如下参数
		// ?TEST=hello