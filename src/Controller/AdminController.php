<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 5/11/18
 * Time: 12:11 PM
 */

namespace App\Controller;

use App\Entity\PostMedia;
use App\Entity\WallPainting;
use App\Manager\SiteDataTransferManager;
use App\Repository\WallPaintingRepository;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    function get_extension($file) {
        $tokens = explode(".", $file);
        $extension = end($tokens);
        return $extension ? $extension : false;
    }


    /**
     * @Route("/admin/transfer-data-from-old-site", name="admin_transfer")
     */
    public function transferOldSite(SiteDataTransferManager $siteDataTransferManager, WallPaintingRepository $wallPaintingRepository)
    {
        $siteDataTransferManager->authentication();
      
        $response = new StreamedResponse();
        $response->setCallback(function() use ($siteDataTransferManager) {

            $html = <<<EOT
<!doctype html>
<html>
<head>
<script>
var p;
</script>
</head>
<body>

</body>
</html>
EOT;
            echo $html;
            ob_flush();
            flush();

            $script = <<<EOT
<script>
    p = document.createElement('p');
    p.innerHTML = 'status:%s - info: %s';
    document.body.appendChild(p);
</script>
EOT;

            ob_flush();
            flush();
            while (true) {
                $result = $siteDataTransferManager->process();
                echo sprintf($script, $result['flag'], $result['info']);
                ob_flush();
                flush();

                if ($result['flag'] == 'NO') {
                    break;
                }
            }
        });

        return $response;
    }

}