<?php

namespace PHPAide\User;

use Exception;

class UserManager {
	/** @var IUserRepository */
	protected $repo;

	protected $passwordPolicy;

	public function __construct( IUserRepository $repo, $options ) {
		$this->repo = $repo;

		$this->setOptions( $options );
	}

	public function getUserByUsername( string $username ): IUser {
		$user = $this->repo->getFromUsername( $username );

		return $user ?? new User( $username );
	}

	public function getUserById( int $id ): ?IUser {
		return $this->repo->getFromId( $id );
	}

	public function saveUser( IUser $user ): ?IUser {
		if ( $user->exists() ) {
			return $this->repo->updateUser( $user ) ? $user : false;
		}
		$id = $this->repo->insertUser( $user );

		if ( !$id ) {
			throw new Exception( 'Cannot save user: ' . $user->getName() );
		}
		$savedUser = new User( $user->getName(), $id );
		$savedUser->setEmail( $user->getEmail() );
		$savedUser->setMailVerified( $user->isEmailVerified() );
		$savedUser->setData( $user->getData() );

		return $savedUser;
	}

	public function setUserPassword( IUser $user, string $password ) {
		if ( !$user->exists() ) {
			throw new Exception( 'Cannot set a password to non-existing user' );
		}

		if ( $this->passwordValid( $password ) ) {
			throw new Exception( "Password '$password' is not valid" );
		}

		$hash = md5( $password );

		$this->repo->setPassword( $user, $hash );
	}

	public function verifyPassword( IUser $user, string $password ): bool {
		$savedHash = $this->repo->getPassword( $user );
		if ( !$savedHash ) {
			throw new Exception( 'User ' . $user->getName() . ' does not have a password saved' );
		}

		return $this->passwordValid( $password ) && $savedHash === md5( $password );
	}

	public function passwordValid( $password ) {
		return (bool) preg_match( $this->passwordPolicy['pattern'], $password );
	}

	private function setOptions( $options ) {
		$defaults = [
			'passwordPolicy' => [
				'pattern' => '/(?=.{8,})(?=.*[A-Z])/'
			]
		];

		$options = array_replace_recursive( $defaults, $options );

		$this->passwordPolicy = $options['passwordPolicy'];
	}
}
