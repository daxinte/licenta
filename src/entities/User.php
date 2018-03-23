<?php
namespace Entity;

use 
    \Spot\Entity,
    \Spot\Mapper,
    \Spot\MapperInterface,
    \Spot\EntityInterface,
    \Spot\EventEmitter
    ;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;


use Util\Util;

class User extends \Spot\Entity implements AdvancedUserInterface, \Serializable
{

    protected static $mapper    = 'Mapper\User';
    protected static $table     = 'users';

    public static function fields()
    {
        return [
            'id'           => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'email'        => ['type' => 'string', 'required' => true],
            'password'     => ['type' => 'string', 'required' => false],
            'salt'         => ['type' => 'string', 'required' => false],
            'roles'        => ['type' => 'simple_array', 'required' => false],
            'name'         => ['type' => 'string', 'required' => false],
            'time_created' => ['type' => 'datetime'],
            'username'     => ['type' => 'string'],
            'isEnabled'    => ['type' => 'boolean', 'required' => false],
        ];
    }

    public static function events( EventEmitter $eventEmitter ) {

        $eventEmitter->on('beforeInsert', function ( Entity $entity, Mapper $mapper ){

        } );

        $eventEmitter->on('afterSave', function ( Entity $entity, Mapper $mapper ){

        } );

    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool    true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool    true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool    true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * Users are enabled by default.
     *
     * @return bool    true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Returns the roles granted to the user. Note that all users have the ROLE_USER role.
     *
     * @return array A list of the user's roles.
     */
    public function getRoles()
    {
        $roles = $this->get('roles');

        // Every user must have at least one role, per Silex security docs.
        //$roles[] = 'ROLE_USER';

        //return array_unique($roles);
    }

    /**
     * Set the user's roles to the given list.
     *
     * @param array $roles
     */
    public function setRoles(array $roles)
    {

        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function addRole($role)
    {
        $role = strtoupper($role);

        if ($role === 'ROLE_USER') {
            return;
        }

        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }
    }

    function hasRole($role) {

        $roles = empty($this->get('roles')) ? [] : $this->get('roles');
        return in_array($role, $roles);
    }

    function getUsername() {
        return $this->get('username');
    }

    function getPassword() {
        return $this->get('password');
    }

    function getSalt() {
        return $this->get('salt');
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is a no-op, since we never store the plain text credentials in this object.
     * It's required by UserInterface.
     *
     * @return void
     */
    public function eraseCredentials()
    {
    }

    /**
     * The Symfony Security component stores a serialized User object in the session.
     * We only need it to store the user ID, because the user provider's refreshUser() method is called on each request
     * and reloads the user by its ID.
     *
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            ) = unserialize($serialized);
    }
}
