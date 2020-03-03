<?php

namespace PHPAide\User;

interface IUser {
	/**
	 * @param string $name
	 * @param int $id
	 */
	public function __construct( string $name, int $id = null );

	/**
	 * Does the user exist in storage
	 * @return bool
	 */
	public function exists(): bool;

	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @return string
	 */
	public function getEmail(): string;

	/**
	 * @return int
	 */
	public function getId(): int;

	/**
	 * Arbitrary data related to user
	 *
	 * @return array
	 */
	public function getData(): array;

	/**
	 * Set arbitrary data for user
	 *
	 * @param array $data
	 */
	public function setData( array $data );

	/**
	 * Get single data item for user
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getDataItem( string $key );

	/**
	 * Set single data item for user
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function setDataItem( string $key, $value );

	/**
	 * @param string $email
	 */
	public function setEmail( string $email );

	/**
	 * @return bool
	 */
	public function isEmailVerified(): bool;

	/**
	 * @param bool $verified
	 */
	public function setMailVerified( bool $verified );
}
