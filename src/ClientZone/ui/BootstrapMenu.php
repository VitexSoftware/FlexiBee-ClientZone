<?php
/**
 * clientzone - Stránka Webu.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace ClientZone\ui;

class BootstrapMenu extends \Ease\TWB\Navbar
{
    /**
     * Navigace.
     *
     * @var \Ease\Html\UlTag
     */
    public $nav = null;

    /**
     * Hlavní menu aplikace.
     *
     * @param string $name
     * @param mixed  $content
     * @param array  $properties
     */
    public function __construct($name = null, $content = null,
                                $properties = null)
    {
        parent::__construct('Menu',
            new \Ease\Html\ImgTag('images/clientzone-logo.svg',
            constant('EASE_APPNAME'),
            ['class' => 'img-rounded', 'height' => 24, 'width' => 24]),
            ['class' => 'navbar-fixed-top']);

        $user = \Ease\Shared::user();
        \Ease\TWB\Part::twBootstrapize();

        switch (get_class($user)) {
            case 'ClientZone\User': //Admin

                $adminMenu = '<li class="dropdown" style="width: 120px; text-align: right; background-image: url( '.$user->getIcon().' ) ;  background-repeat: no-repeat; background-position: left center; background-size: 40px 40px;"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$user->getUserLogin().' <b class="caret"></b></a>
<ul class="dropdown-menu" style="text-align: left; left: -60px;">
<li><a href="settings.php">'.\Ease\TWB\Part::GlyphIcon('wrench').'<i class="icon-cog"></i> '._('Settings').'</a></li>
';


                if ($user->getSettingValue('admin')) { //Superuser
//                    $userMenu .= '<li><a href="overview.php">'.\Ease\TWB\Part::GlyphIcon('list').' '._('Admin Item').'</a></li>';
                }

                $this->addMenuItem($adminMenu.'
<li><a href="changepassword.php">'.\Ease\TWB\Part::GlyphIcon('lock').' '._('Password change').'</a></li>
<li><a href="about.php">'.\Ease\TWB\Part::GlyphIcon('info-sign').' '._('About').'</a></li>
<li class="divider"></li>
<li><a href="logout.php">'.\Ease\TWB\Part::GlyphIcon('off').' '._('Sign Out').'</a></li>
</ul>
</li>
', 'right');
                break;
            case 'ClientZone\Customer': //Customer
                $userMenu = '<li class="dropdown" style="width: 120px; text-align: right; background-image: url( '.$user->getIcon().' ) ;  background-repeat: no-repeat; background-position: left center; background-size: 40px 40px;"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$user->getUserLogin().' <b class="caret"></b></a>
<ul class="dropdown-menu" style="text-align: left; left: -60px;">
<!-- li><a href="settings.php">'.\Ease\TWB\Part::GlyphIcon('wrench').'<i class="icon-cog"></i> '._('Settings').'</a></li -->
';

                if (isset($_SESSION['cart']) && count($_SESSION['cart'])) {
                    $this->addMenuItem(new \Ease\TWB\LinkButton('cart.php',
                        '<img width=30 src=images/cart.svg> '._('Cart').' '.count($_SESSION['cart'])),
                        'right');
                }

                $this->addMenuItem($userMenu.'
<li><a href="changecustpw.php">'.\Ease\TWB\Part::GlyphIcon('lock').' '._('Password change').'</a></li>
<li class="divider"></li>
<li><a href="logout.php">'.\Ease\TWB\Part::GlyphIcon('off').' '._('Sign Out').'</a></li>
</ul>
</li>
', 'right');
                break;
            case 'Ease\Anonym': //Anonymous
            default:
//                $this->addMenuItem('<a href="createaccount.php">'.\Ease\TWB\Part::GlyphIcon('leaf').' '._('Registrace').'</a>',
//                    'right');

                if (isset($_SESSION['cart']) && count($_SESSION['cart'])) {
                    $this->addMenuItem(new \Ease\TWB\LinkButton('cart.php',
                        '<img width=30 src=images/cart.svg> '._('Cart').' '.count($_SESSION['cart'])),
                        'right');
                }

                $this->addMenuItem(
                    '
<li class="divider-vertical"></li>
<li class="dropdown">
<a class="dropdown-toggle" href="login.php" data-toggle="dropdown"><i class="icon-circle-arrow-left"></i> '._('Sign In').'<strong class="caret"></strong></a>
<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px; left: -120px;">
<form method="post" class="navbar-form navbar-left" action="login.php" accept-charset="UTF-8">
<input style="margin-bottom: 15px;" type="text" placeholder="'._('Login').'" id="username" name="email">
<input style="margin-bottom: 15px;" type="password" placeholder="'._('Password').'" id="password" name="password">
<!-- input style="float: left; margin-right: 10px;" type="checkbox" name="remember-me" id="remember-me" value="1">
<label class="string optional" for="remember-me"> '._('Remeber me').'</label -->
<input class="btn btn-primary btn-block" type="submit" id="sign-in" value="'._('Sign In').'">
<!-- a href="newflexibeeuser.php" class="btn btn-info btn-block"><i class="fa fa-user"></i> '._('Registrace').'</a -->
</form>
</div>', 'right'
                );


                break;
        }
    }

    /**
     * Show status messages
     */
    public function draw()
    {
        $webPage = \Ease\Shared::webPage();
        $statusMessages = $webPage->getStatusMessagesAsHtml();
        if ($statusMessages) {
            $this->addItem(new \Ease\Html\DivTag($statusMessages,
                ['id' => 'StatusMessages', 'class' => 'well', 'title' => _('Click to hide messages'),
                'data-state' => 'down']));
            $this->addItem(new \Ease\Html\DivTag(null, ['id' => 'smdrag']));
            $webPage->cleanMessages();
        }
        parent::draw();
    }

}
