<?php
	
	class StreamManager_UnitTestCase extends WP_UnitTestCase {

		public function buildPosts($count = 10) {
			$post_ids = array();
			for ($i = 0; $i<$count; $i++) {
				$date = date("Y-m-d H:i:s", strtotime('-'.$i.' days'));
				$post_ids[] = $this->factory->post->create(array('post_date' => $date));
			}
			return $post_ids;
		}

		public function buildStream() {
			$pid = $this->factory->post->create(array('post_type' => 'sm_stream', 'post_content' => '', 'post_title' => 'Sample Stream'));
			$stream = new TimberStream($pid);
			return $stream;
		}

	}