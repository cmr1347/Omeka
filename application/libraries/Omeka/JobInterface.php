<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Interface for jobs.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2010
 */
interface Omeka_JobInterface
{
    public function __construct(array $options);
    public function perform();
}