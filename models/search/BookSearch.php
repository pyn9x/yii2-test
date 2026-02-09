<?php
namespace app\models\search;

use app\models\Book;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BookSearch extends Book
{
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['title', 'isbn', 'description', 'published_at'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * @param array<string, mixed> $params
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Book::find()->with('authors');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'published_at' => $this->published_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'isbn', $this->isbn])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
