<?php

/**
 * Shared interface for interactions. 
 */
interface Model {
  /**
   * Load a model.
   * @param int $address_id 
   */
  static function load($component_id);
  
  /**
   * Save a model. 
   */
  function save();
  
  /**
   * Delete a model. 
   */
  function delete();
}