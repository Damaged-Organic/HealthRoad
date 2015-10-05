<?php
// src/AppBundle/Entity/Setting/SettingType.php:2
namespace AppBundle\Entity\Setting;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Setting\Repository\SettingRepository")
 */
class Setting
{
    use IdMapperTrait;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Setting\SettingDecimal", mappedBy="setting")
     */
    protected $settingsDecimal;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Setting\SettingString", mappedBy="setting")
     */
    protected $settingsString;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->settingsDecimal = new \Doctrine\Common\Collections\ArrayCollection();
        $this->settingsString = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add settingDecimal
     *
     * @param \AppBundle\Entity\Setting\SettingDecimal $settingDecimal
     * @return Setting
     */
    public function addSettingsDecimal(\AppBundle\Entity\Setting\SettingDecimal $settingDecimal)
    {
        $settingDecimal->setSetting($this);
        $this->settingsDecimal[] = $settingDecimal;

        return $this;
    }

    /**
     * Remove settingsDecimal
     *
     * @param \AppBundle\Entity\Setting\SettingDecimal $settingsDecimal
     */
    public function removeSettingsDecimal(\AppBundle\Entity\Setting\SettingDecimal $settingsDecimal)
    {
        $this->settingsDecimal->removeElement($settingsDecimal);
    }

    /**
     * Get settingsDecimal
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSettingsDecimal()
    {
        return $this->settingsDecimal;
    }

    /**
     * Add settingString
     *
     * @param \AppBundle\Entity\Setting\SettingString $settingString
     * @return Setting
     */
    public function addSettingsString(\AppBundle\Entity\Setting\SettingString $settingString)
    {
        $settingString->setSetting($this);
        $this->settingsString[] = $settingString;

        return $this;
    }

    /**
     * Remove settingsString
     *
     * @param \AppBundle\Entity\Setting\SettingString $settingsString
     */
    public function removeSettingsString(\AppBundle\Entity\Setting\SettingString $settingsString)
    {
        $this->settingsString->removeElement($settingsString);
    }

    /**
     * Get settingsString
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSettingsString()
    {
        return $this->settingsString;
    }
}