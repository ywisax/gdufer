������벢�������֮�������Ҫ���ĵ�ǰ���л����������ˡ�

��ȫ������[Bootstrap�½�](bootstrap)��

## ������������

����Ҫ��Ӧ����ʽͶ��Ӧ��֮ǰ���㻹��Ҫ����һЩϸ΢���õ��޸ġ�

1. �鿴�ĵ��е�[Bootstrap�½�](bootstrap)��
   ������ѡ����Ѳ���Ҫ�Ĺر��ˡ�
   ���磬��Ҫ��������·�����湦�ܲ��ر�Profiling��[Kohana::init]�����ã��� 
   ������кܶ�·�ɣ���ô[Route::cache]Ҳ������������
2. ����APC������opcode������
   ��������PHP���ܣ���������׵ķ�����
   Խ�Ǹ��ӵ�Ӧ�ã�ʹ��opcode����ĺô���Խ�ࡣ

		/**
		 * �����������жϵ�ǰ������Ĭ����Kohana::DEVELOPMENT����
		 */
		Kohana::$environment = ($_SERVER['SERVER_NAME'] !== 'localhost') ? Kohana::PRODUCTION : Kohana::DEVELOPMENT;
		/**
		 * ���ݻ�������ʼ��ѡ��
		 */
		Kohana::init(array(
			'base_url'   => '/',
			'index_file' => FALSE,
			'profile'    => Kohana::$environment !== Kohana::PRODUCTION,
			'caching'    => Kohana::$environment === Kohana::PRODUCTION,
		));
