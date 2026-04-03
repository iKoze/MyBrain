<?php
/**
 * @brief hieratical file template provider
 * @author Florian Schießl <florian@floriware.de> (29.4.2016)
 *
 * Mode of operation
 * =================
 * The MyBrain Templater takes in a (single) template file (constructor or 
 * setTemplate()), and some content as defined by the following methods:
 * * handle()
 *   this is a wrapper, which calls getContent() of an iTemplateUser. Useful if 
 *   you'd like to pass a controller to the Templater
 * * content()
 *   simply set the content which has to be shown within the template
 * * require_file()
 *   require a file within the template
 * * eval_code()
 *   evaluate code within the template
 *
 * Template files
 * --------------
 * Template files always describe the outer area of a page. If you take a HTML
 * page for example, the main template contains the outer <html> tags, and the
 * content is within <body>.
 * A template file requires 'template-job.php' at the point where the content
 * get's expanded.
 *
 * ~~~~~{.html}
 *     <html><body>
 *     <div class=navi><a href...></div>
 *     <div class=content>
 *     <?php require 'template-job.php' ?>
 *     </div>
 *     </body></html>
 * ~~~~~
 *
 * Example
 * -------
 * The following code with the upper template would result in a page which
 * looks like this:
 * 
 * ~~~~~{.php}
 *     $t = new Templater("content/templates", "template.php");
 *     $t->content("Hello World");
 *     $t->render();
 * ~~~~~
 *
 * ~~~~~{.html}
 *     <html><body>
 *     <div class=navi><a href...></div>
 *     <div class=content>
 *     Hello World
 *     </div>
 *     </body></html>
 * ~~~~~
 *
 */
namespace Floriware\MyBrain\Core;

use Floriware\MyBrain\Interface\iTemplateUser;

class Templater
{
	/** @brief path to the template */
	public $template_path = "";

	/** @brief current filename of the template */
	public $template_name = "";

	/** @brief global namespace for every view */
	public $global_ns = null;

	/** @brief content defined by handle(), content(), require_file() or
	 * eval_code()
	 */
	public $content = null;

	/** @brief use this to disable template */
	public $no_template = false;

	/** @brief new Templater
	 * @param string $template_path: Path where templates persist within
	 * @param string $template_name: Name of the template file
	 * @param array $global_ns: variables defined for every view.
	 * Use $global_ns to add variables to the namespace for views, which aren't
	 * defined by their controller. Eg. variables like the logged in user,
	 * current time or session.
	 */
	public function __construct($template_path,
		$template_name,
		$global_ns=array())
	{
		$this->template_path = $template_path;
		$this->template_name = $template_name;
		$this->global_ns = $global_ns;
	}

	/** @brief change the template */
	public function setTemplate($template_name)
	{
		$this->template_name = $template_name;
	}

	/** @brief get a new Templater like this one, but with other template */
	public function getNewTemplater($template_name = null)
	{
		if ($template_name === null) $template_name = $this->template_name;
		return new Templater($this->template_path,
			$template_name,
			$this->global_ns);
	}

	/** @brief add a new variable to $global_ns */
	public function registerVar($name, $var)
	{
		$this->global_ns[$name] = $var;
	}

	/** @brief handle a controller.
	 * The controller must implement iTemplateUser. It then decides whether to 
	 * call content(), require_file() or eval_code() for this controller.
	 * @see iTemplateUser
	 * @note If the controller doesn't implement iTemplateUser, the content
	 * will stay empty.
	 */
	public function handle($c)
	{
		if (!$c instanceof iTemplateUser) return;
		list($job, $content, $file) = $c->getContent();
		if ($job == "content") $this->content($content);
		if ($job == "eval") $this->eval_code($content);
		if ($job == "require") $this->require_file($file, $content);
	}

	/** @brief require a content file within the template
	 * @param string $file: path to the content file
	 * @param array $content: content namespace
	 */
	public function require_file($file, $content = array())
	{
		extract($this->global_ns, EXTR_OVERWRITE);
		extract($content, EXTR_OVERWRITE);
		ob_start();
		require($file);
		$this->content = ob_get_contents();
		ob_end_clean();
	}

	/** @brief eval code within the template */
	public function eval_code($code)
	{
		extract($this->global_ns, EXTR_OVERWRITE);
		ob_start();
		eval($code);
		$this->content = ob_get_contents();
		ob_end_clean();
	}

	/** @brief simply set the content (text). If content is another templater, 
	 * use it's result of getRender() as content.
	 * @warning make sure not to pass a templater to it's own content()
	 * function, as this results in a recursion loop.
	 */
	public function content($content)
	{
		$this->job = "content";
		if ($content instanceof Templater)
		{
			$this->content = $content->getRender();
			return;
		}
		$this->content = $content;
	}

	/** @brief get the rendered result as string.
	 * @return content
	 */
	public function getRender()
	{
		ob_start();
		$this->render();
		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}

	/** @brief get full path to template file */
	public function getRequire()
	{
		return $this->template_path."/".$this->template_name;
	}

	/** @brief render template with content. This directly prints. Use
	 * getRender() if you need the result as string.
	 */
	public function render()
	{
		if ($this->content === null) return false;
		if ($this->no_template === true) {
			# no template requested
			echo $this->content;
			return;
		}
		extract($this->global_ns, EXTR_OVERWRITE);
		require($this->getRequire());
	}
}
