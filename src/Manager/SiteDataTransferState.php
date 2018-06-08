<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 5/27/18
 * Time: 5:37 AM
 */

namespace App\Manager;


class SiteDataTransferState implements \Serializable
{
    const PHASE_PORTFOLIO_CATEGORY = 1;
    const PHASE_CUSTOMER = 2;
    const PHASE_TAG = 3;
    const PHASE_PORTFOLIO = 4;

    private $phase;
    private $page;
    //category
    //customer
    //tag
    //wallpainting

    //category
    //message

    public static function instance()
    {
        $object = null;

        if (file_exists('transfer.log')) {
            $s = file_get_contents('transfer.log');
            $object = unserialize($s);

        }


        if (!is_object($object) || !$object instanceof SiteDataTransferState) {
            return new SiteDataTransferState();
        }
        return $object;
    }

    public function __construct()
    {
        $this->phase = self::PHASE_PORTFOLIO_CATEGORY;
        $this->page = 1;
    }

    public function save()
    {

        $s = serialize($this);
        file_put_contents('transfer.log', $s);
    }

    public function nextPhase()
    {
        $this->phase ++;

    }

    /**
     * @return int
     */
    public function getPhase(): int
    {
        return $this->phase;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page): void
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    public function incPage()
    {
        $this->page ++;
    }

    public function resetPage()
    {
        $this->page = 1;
    }


    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {

        return serialize([
            $this->phase,
            $this->page
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->phase,
            $this->page
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }
}