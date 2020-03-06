<?php

namespace PHPAide\User;

use Exception;
use JsonSerializable;

class User implements IUser {
	/** @var int  */
	protected $id = 0;
	/** @var string  */
	protected $name = '';
	/** @var string  */
	protected $email = '';
	/** @var array  */
	protected $data = [];
	/** @var bool  */
	protected $emailVerified = false;

	/**
	 * @inheritDoc
	 */
	public function __construct( string $name, int $id = null ) {
		$this->name = $name;
		$this->id = $id ?? 0;
	}

	/**
	 * @inheritDoc
	 */
	public function exists(): bool {
		return $this->getId() !== 0;
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @inheritDoc
	 */
	public function getEmail(): string {
		return $this->email;
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @inheritDoc
	 */
	public function getData(): array {
		return $this->data;
	}

	/**
	 * @inheritDoc
	 */
	public function setData( array $data ) {
		if ( !is_array( $data ) ) {
			throw new Exception( 'Data passed to ' . __METHOD__ . ' must be an array!' );
		}

		$this->data = $data;
	}

	/**
	 * @inheritDoc
	 */
	public function getDataItem( string $key ) {
		if ( isset( $this->data[$key] ) ) {
			return $this->data[$key];
		}

		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function setDataItem( string $key, $value ) {
		if ( is_object( $value ) && !$value instanceof JsonSerializable ) {
			throw new Exception( 'Data item set to a user must be instance of ' . JsonSerializable::class );
		}
		$this->data[$key] = $value;
	}

	/**
	 * @inheritDoc
	 */
	public function setEmail( string $email ) {
		if ( !$this->isValidEmail( $email ) ) {
			throw new Exception( "Email $email is not a valid email address" );
		}

		$this->email = filter_var( $email, FILTER_SANITIZE_EMAIL );
	}

	/**
	 * @inheritDoc
	 */
	public function isEmailVerified(): bool {
		return $this->emailVerified;
	}

	/**
	 * @inheritDoc
	 */
	public function setMailVerified( bool $verified ) {
		if ( $verified && !$this->isValidEmail( $this->email ) ) {
			return;
		}
		$this->emailVerified = $verified;
	}

	protected function isValidEmail( $email ) {
		return $email !== '' && filter_var( $email, FILTER_VALIDATE_EMAIL );
	}

	/**
	 * @inheritDoc
	 */
	public function cloneWithId( $id ): IUser {
		$newUser = new static( $this->getName(), $id );
		if ( $this->getEmail() ) {
			$newUser->setEmail( $this->getEmail() );
			$newUser->setMailVerified( $this->isEmailVerified() );
		}
		$newUser->setData( $this->getData() );

		return $newUser;
	}
}
